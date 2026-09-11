<?php

namespace App\Services;

use App\Models\RideRequest;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;
use Razorpay\Api\Api;

class RazorpayPaymentService
{
    /**
     * Razorpay credentials come only from the admin Payment Settings
     * (settings table, key "razorpay"). The gateway must be enabled there.
     */
    public static function credentials(): array
    {
        static $cached = null;

        if ($cached !== null) {
            return $cached;
        }

        $disabled = ['key' => null, 'secret' => null, 'webhook_secret' => null, 'enabled' => false];

        $setting = Setting::where('key', 'razorpay')->first();

        if (!$setting || (int) $setting->status_id !== 1) {
            return $cached = $disabled;
        }

        $values = json_decode($setting->value, true) ?: [];

        if (empty($values['key_id']) || empty($values['key_secret'])) {
            return $cached = $disabled;
        }

        return $cached = [
            'key'            => $values['key_id'],
            'secret'         => $values['key_secret'],
            'webhook_secret' => $values['webhook_secret'] ?: null,
            'enabled'        => true,
        ];
    }

    private function keyId(): ?string
    {
        return self::credentials()['key'];
    }

    private function api(): Api
    {
        $credentials = self::credentials();

        if (!$credentials['enabled'] || empty($credentials['key']) || empty($credentials['secret'])) {
            throw new \RuntimeException('Razorpay is not enabled in Payment Settings.');
        }

        return new Api($credentials['key'], $credentials['secret']);
    }

    public function createRideOrder(RideRequest $ride): array
    {
        $payable = (float) ($ride->advance_amount ?? $ride->estimated_fare);
        $amount = (int) round($payable * 100);

        if ($amount <= 0) {
            throw new \InvalidArgumentException('Invalid ride amount for payment.');
        }

        if (!empty($ride->razorpay_order_id) && $ride->payment_status === 'unpaid') {
            try {
                $existing = $this->api()->order->fetch($ride->razorpay_order_id);
                $status = (string) ($existing['status'] ?? '');
                $existingAmount = (int) ($existing['amount'] ?? 0);

                if (in_array($status, ['created', 'attempted'], true) && $existingAmount === $amount) {
                    return [
                        'order_id' => $existing['id'],
                        'amount' => $existingAmount,
                        'currency' => $existing['currency'] ?? 'INR',
                        'key' => $this->keyId(),
                    ];
                }
            } catch (\Throwable $e) {
                Log::warning('fairprice_order.reuse_failed', [
                    'ride_id' => $ride->id,
                    'razorpay_order_id' => $ride->razorpay_order_id,
                    'message' => $e->getMessage(),
                ]);
            }
        }

        $order = $this->api()->order->create([
            'receipt' => 'ride_' . $ride->id . '_' . time(),
            'amount' => $amount,
            'currency' => 'INR',
            'notes' => [
                'ride_id' => (string) $ride->id,
                'booking_type' => (string) $ride->booking_type,
            ],
        ]);

        $ride->update([
            'razorpay_order_id' => $order['id'],
            'advance_amount' => round($amount / 100, 2),
            'payment_status' => 'unpaid',
        ]);

        return [
            'order_id' => $order['id'],
            'amount' => $amount,
            'currency' => 'INR',
            'key' => $this->keyId(),
        ];
    }

    public function createInstantRideOrder(RideRequest $ride, float $amountRupees): array
    {
        $amount = (int) round($amountRupees * 100);

        if ($amount <= 0) {
            throw new \InvalidArgumentException('Invalid ride amount for payment.');
        }

        $order = $this->api()->order->create([
            'receipt' => 'instant_ride_' . $ride->id . '_' . time(),
            'amount' => $amount,
            'currency' => 'INR',
            'notes' => [
                'ride_id' => (string) $ride->id,
                'booking_type' => 'instant',
            ],
        ]);

        $ride->update([
            'razorpay_order_id' => $order['id'],
            'advance_amount' => round($amount / 100, 2),
            'payment_status' => 'unpaid',
        ]);

        $qrCodeUrl = null;
        $qrCodeId = null;

        try {
            $qrCode = $this->api()->qrCode->create([
                'type' => 'upi_qr',
                'name' => 'Ride ' . $ride->id,
                'usage' => 'single_use',
                'fixed_amount' => true,
                'payment_amount' => $amount,
                'description' => 'Instant ride payment for ride #' . $ride->id,
            ]);

            $qrCodeId = $qrCode['id'] ?? null;
            $qrCodeUrl = $qrCode['image_url'] ?? ($qrCode['image_url_png'] ?? null);
        } catch (\Throwable $e) {
            Log::warning('instant_payment.qr_generation_failed', [
                'ride_id' => $ride->id,
                'message' => $e->getMessage(),
            ]);
        }

        return [
            'order_id' => $order['id'],
            'amount' => $amount,
            'currency' => 'INR',
            'key' => $this->keyId(),
            'qr_code_id' => $qrCodeId,
            'qr_code_url' => $qrCodeUrl,
        ];
    }

    public function verifyAndCapture(RideRequest $ride, string $orderId, string $paymentId, string $signature): void
    {
        $this->api()->utility->verifyPaymentSignature([
            'razorpay_order_id' => $orderId,
            'razorpay_payment_id' => $paymentId,
            'razorpay_signature' => $signature,
        ]);

        $ride->update([
            'razorpay_order_id' => $orderId,
            'razorpay_payment_id' => $paymentId,
            'payment_status' => 'paid',
            'paid_at' => now(),
            'advance_amount' => $ride->advance_amount ?? $ride->estimated_fare,
        ]);
    }

    public function refundRidePayment(RideRequest $ride, ?float $refundAmountRupees = null): bool
    {
        if ($ride->payment_status !== 'paid' || empty($ride->razorpay_payment_id)) {
            return false;
        }

        try {
            $paid = (float) ($ride->advance_amount ?? $ride->estimated_fare);
            $refundRupees = $refundAmountRupees !== null ? $refundAmountRupees : $paid;
            $refundRupees = min($refundRupees, $paid);

            if ($refundRupees <= 0) {
                $ride->update([
                    'payment_status' => 'refunded',
                    'refund_amount' => 0,
                ]);

                return true;
            }

            $amount = (int) round($refundRupees * 100);
            $refund = $this->api()->payment->fetch($ride->razorpay_payment_id)->refund([
                'amount' => $amount,
            ]);

            $ride->update([
                'payment_status' => 'refunded',
                'razorpay_refund_id' => $refund['id'] ?? null,
                'refund_amount' => round($refundRupees, 2),
            ]);

            return true;
        } catch (\Throwable $e) {
            Log::error('fairprice_refund.failed', [
                'ride_id' => $ride->id,
                'message' => $e->getMessage(),
            ]);

            return false;
        }
    }
}
