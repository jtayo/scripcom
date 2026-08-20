<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    public function send(string $phone, string $message): bool
    {
        $provider = config('services.sms.provider', 'tolclin');

        return match ($provider) {
            'tolclin' => $this->sendViaTolclin($phone, $message),
            'log' => $this->sendViaLog($phone, $message),
            default => false,
        };
    }

    private function sendViaTolclin(string $phone, string $message): bool
    {
        $url = config('services.sms.tolclin.url', 'https://tolclin.com/tolclin/sms/BulkSms');
        $token = config('services.sms.tolclin.token');
        $clientId = config('services.sms.tolclin.client_id');
        $senderId = config('services.sms.tolclin.sender_id', 'COUNTY-MSA');
        $callback = config('services.sms.tolclin.callback_url', '');

        if (! $token) {
            Log::warning('TolClin SMS token not configured.');

            return false;
        }

        $msisdn = $this->normalizeMsisdn($phone);

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $token,
            ])->timeout(15)->post($url, [
                'clientid' => $clientId,
                'callbackurl' => $callback,
                'senderid' => $senderId,
                'msisdn' => $msisdn,
                'message' => $message,
            ]);

            $body = $response->json();

            if ($response->successful()) {
                return true;
            }

            Log::warning('TolClin SMS failed', ['status' => $response->status(), 'response' => $body]);

            return false;
        } catch (\Throwable $e) {
            Log::warning('TolClin SMS error', ['error' => $e->getMessage()]);

            return false;
        }
    }

    private function sendViaLog(string $phone, string $message): bool
    {
        Log::info('SMS sent (logged)', ['phone' => $phone, 'message' => $message]);

        return true;
    }

    private function normalizeMsisdn(string $phone): string
    {
        $phone = trim($phone);

        if (str_starts_with($phone, '+')) {
            return substr($phone, 1);
        }

        if (str_starts_with($phone, '254')) {
            return $phone;
        }

        if (str_starts_with($phone, '0')) {
            return '254' . substr($phone, 1);
        }

        return '254' . $phone;
    }
}
