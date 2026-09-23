<?php

namespace App\Notifications;

use App\Models\Shop\Order;
use App\Services\CommonFirebaseNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

/**
 * Tells a customer their order has moved.
 *
 * Two places at once:
 *   - saved on the site, so they can find it under My Account days later
 *   - pushed to their phone, if the app has registered a device token
 *
 * SMS is deliberately not here yet.
 *
 * Nothing in here is allowed to fail an order update. Push in particular
 * depends on Google being reachable and on the credentials file being present,
 * neither of which is true on every machine, so it is wrapped and logged.
 */
class OrderStatusChanged extends Notification
{
    use Queueable;

    public function __construct(
        private Order $order,
        private string $status,
        private ?string $note = null,
    ) {
    }

    /** Saved on the site. The push is sent from toDatabase, see below. */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $this->push($notifiable);

        return [
            'type'         => 'order_status',
            'order_id'     => $this->order->id,
            'order_number' => $this->order->order_number,
            'status'       => $this->status,
            'title'        => $this->title(),
            'body'         => $this->body(),
            'note'         => $this->note,
            'url'          => route('site.account.order', $this->order->id),
        ];
    }

    /** What the customer reads. Plain words, no jargon. */
    private function title(): string
    {
        return match ($this->status) {
            'confirmed' => 'Order confirmed',
            'packed'    => 'Order packed',
            'shipped'   => 'Order on its way',
            'delivered' => 'Order delivered',
            'cancelled' => 'Order cancelled',
            default     => 'Order updated',
        };
    }

    private function body(): string
    {
        $number = $this->order->order_number;

        $line = match ($this->status) {
            'confirmed' => "We have confirmed order {$number} and are getting it ready.",
            'packed'    => "Order {$number} is packed and waiting to be picked up.",
            'shipped'   => "Order {$number} is on its way to you.",
            'delivered' => "Order {$number} has been delivered. Thank you!",
            'cancelled' => "Order {$number} has been cancelled.",
            default     => "There is an update on order {$number}.",
        };

        return $this->note ? $line . ' ' . $this->note : $line;
    }

    /** Push to the phone, if there is one. Never throws. */
    private function push(object $notifiable): void
    {
        try {
            $token = $notifiable->device_token ?? null;

            if (! $token) {
                return;
            }

            app(CommonFirebaseNotification::class)->sendCommonNotification(
                $token,
                $this->title(),
                $this->body(),
                [
                    'type'         => 'order_status',
                    'order_id'     => (string) $this->order->id,
                    'order_number' => (string) $this->order->order_number,
                    'status'       => (string) $this->status,
                ],
                $notifiable->getKey(),
            );
        } catch (\Throwable $e) {
            // A push that cannot be sent is never a reason to fail an order update.
            Log::warning('Order push notification failed: ' . $e->getMessage());
        }
    }
}
