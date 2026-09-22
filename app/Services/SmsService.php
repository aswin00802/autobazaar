<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class SmsService
{
    /** Gateway address and key both come from .env — see config/services.php. */
    private function gatewayUrl(): string
    {
        return (string) config('services.ping4sms.url', 'http://site.ping4sms.com/api/smsapi');
    }

    private function gatewayKey(): string
    {
        return (string) config('services.ping4sms.key');
    }

    private const OTP_SENDER = 'PNGOTP';
    private const OTP_TEMPLATE_ID = '1507165967974501361';

    private const SOS_SENDER = 'EYETIR';
    private const SOS_TEMPLATE_ID = '1677100000000390421';

    public function send(string $number, string $message): bool
    {
        return $this->dispatch('POST', $number, $message, self::OTP_SENDER, self::OTP_TEMPLATE_ID);
    }

    /**
     * Portal-matching SOS body:
     * Emergency SOS: {name} needs help on FairPrice ride. Location: {loc}. Call immediately: {phone}.
     *
     * EYETHIRD
     */
    public function sendSos(string $number, string $personName, string $locationText, string $callPhone): bool
    {
        $name = preg_replace('/[^\p{L}\p{N}\s.\'\-]/u', '', trim($personName));
        $name = trim(preg_replace('/\s+/', ' ', (string) $name)) ?: 'A rider';
        $location = trim($locationText) ?: 'Unavailable';
        $phone = preg_replace('/\D+/', '', $callPhone) ?: '0000000000';

        $message = "Emergency SOS: {$name} needs help on FairPrice ride. Location: {$location}. Call immediately: {$phone}.\n\nEYETHIRD";

        return $this->dispatch('GET', $number, $message, self::SOS_SENDER, self::SOS_TEMPLATE_ID);
    }

    private function dispatch(string $method, string $number, string $message, string $sender, string $templateId): bool
    {
        try {
            $client = new Client(['connect_timeout' => 5, 'timeout' => 10]);
            $params = [
                'key'        => $this->gatewayKey(),
                'route'      => 2,
                'sender'     => $sender,
                'number'     => $number,
                'sms'        => $message,
                'templateid' => $templateId,
            ];

            $response = strtoupper($method) === 'GET'
                ? $client->get($this->gatewayUrl(), ['query' => $params])
                : $client->post($this->gatewayUrl(), ['form_params' => $params]);

            $statusCode = $response->getStatusCode();
            $responseBody = trim((string) $response->getBody()->getContents());
            $ok = $statusCode === 200 && $this->isAccepted($responseBody);

            if (!$ok) {
                Log::error('FairPrice SMS rejected.', [
                    'method' => $method,
                    'sender' => $sender,
                    'template_id' => $templateId,
                    'mobile_last_four' => substr($number, -4),
                    'status_code' => $statusCode,
                    'response_body' => $responseBody,
                ]);
            }

            return $ok;
        } catch (\Throwable $e) {
            Log::error('FairPrice SMS failed.', [
                'method' => $method,
                'sender' => $sender,
                'mobile_last_four' => substr($number, -4),
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    private function isAccepted(string $responseBody): bool
    {
        if ($responseBody === '') {
            return false;
        }

        // Ping4 success = long numeric message/job id. Short codes / text = reject.
        if (preg_match('/^\d+$/', $responseBody)) {
            return strlen($responseBody) > 3;
        }

        $normalized = strtolower($responseBody);

        return !(
            str_contains($normalized, 'error')
            || str_contains($normalized, 'invalid')
            || str_contains($normalized, 'fail')
            || str_contains($normalized, 'reject')
            || str_contains($normalized, 'insufficient')
            || str_contains($normalized, 'template')
        );
    }
}
