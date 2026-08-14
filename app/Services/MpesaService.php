<?php

namespace App\Services;

use App\Models\Payment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MpesaService
{
    public function isConfigured(): bool
    {
        return (bool) config('services.mpesa.consumer_key')
            && (bool) config('services.mpesa.consumer_secret');
    }

    public function stkPush(string $phone, float $amount, ?int $sponsorshipId = null): array
    {
        if (! $this->isConfigured()) {
            Log::warning('M-Pesa not configured; simulating STK push', [
                'phone' => $phone,
                'amount' => $amount,
            ]);

            return [
                'success' => true,
                'simulated' => true,
                'checkout_request_id' => 'SIM-' . strtoupper(uniqid()),
            ];
        }

        $token = $this->token();

        if (! $token) {
            return ['success' => false, 'error' => 'Unable to authenticate with M-Pesa'];
        }

        $shortcode = config('services.mpesa.shortcode');
        $passkey = config('services.mpesa.passkey');
        $timestamp = now()->format('YmdHis');
        $password = base64_encode($shortcode . $passkey . $timestamp);

        $response = Http::withToken($token)
            ->timeout(20)
            ->post('https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest', [
                'BusinessShortCode' => $shortcode,
                'Password' => $password,
                'Timestamp' => $timestamp,
                'TransactionType' => 'CustomerPayBillOnline',
                'Amount' => (int) round($amount),
                'PartyA' => $this->normalizePhone($phone),
                'PartyB' => $shortcode,
                'PhoneNumber' => $this->normalizePhone($phone),
                'CallBackURL' => route('api.mpesa.callback', [], true),
                'AccountReference' => 'WIFI-' . ($sponsorshipId ?? 'CREDITS'),
                'TransactionDesc' => 'WiFi sponsorship credits',
            ]);

        $body = $response->json();

        if (! $response->successful() || ($body['ResponseCode'] ?? '1') !== '0') {
            return ['success' => false, 'error' => $body['ResponseDescription'] ?? 'STK push failed'];
        }

        Payment::create([
            'sponsorship_id' => $sponsorshipId,
            'phone' => $phone,
            'amount' => $amount,
            'currency' => 'KES',
            'status' => 'pending',
            'checkout_request_id' => $body['CheckoutRequestID'],
            'transaction_id' => $body['MerchantRequestID'],
        ]);

        return [
            'success' => true,
            'checkout_request_id' => $body['CheckoutRequestID'],
            'merchant_request_id' => $body['MerchantRequestID'],
        ];
    }

    public function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/\D+/', '', $phone);

        if (str_starts_with($phone, '0')) {
            $phone = '254' . substr($phone, 1);
        } elseif (str_starts_with($phone, '7') || str_starts_with($phone, '1')) {
            $phone = '254' . $phone;
        }

        return $phone;
    }

    private function token(): ?string
    {
        $auth = base64_encode(
            config('services.mpesa.consumer_key') . ':' . config('services.mpesa.consumer_secret')
        );

        $response = Http::withHeaders(['Authorization' => 'Basic ' . $auth])
            ->timeout(15)
            ->get('https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials');

        return $response->json('access_token');
    }
}
