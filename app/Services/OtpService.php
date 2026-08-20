<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class OtpService
{
    private const TTL = 600;
    private const MAX_ATTEMPTS = 5;
    private const RESEND_COOLDOWN = 60;

    public function generate(string $phone): string
    {
        $otp = app()->environment('local', 'testing')
            ? config('app.debug_otp', '123456')
            : (string) random_int(100000, 999999);

        Cache::put($this->key($phone), [
            'otp' => $otp,
            'attempts' => 0,
        ], self::TTL);

        return $otp;
    }

    public function sendOtp(string $phone): array
    {
        $cooldownKey = $this->cooldownKey($phone);

        if (Cache::has($cooldownKey)) {
            $remaining = (int) Cache::get($cooldownKey);
            return ['success' => false, 'message' => "Please wait {$remaining}s before requesting another code."];
        }

        $otp = $this->generate($phone);
        $message = "Your SCRIPCOM verification code is: {$otp}. It expires in 10 minutes.";

        $sent = app(SmsService::class)->send($phone, $message);

        if (! $sent) {
            return ['success' => false, 'message' => 'Failed to send verification code. Please try again.'];
        }

        Cache::put($cooldownKey, self::RESEND_COOLDOWN, self::RESEND_COOLDOWN);

        if (app()->environment('local', 'testing')) {
            return ['success' => true, 'message' => 'Verification code sent.', 'debug_otp' => $otp];
        }

        return ['success' => true, 'message' => 'Verification code sent.'];
    }

    public function verify(string $phone, string $otp): bool
    {
        $data = Cache::get($this->key($phone));

        if (! $data) {
            return false;
        }

        $data['attempts'] = ($data['attempts'] ?? 0) + 1;

        if ($data['attempts'] > self::MAX_ATTEMPTS) {
            Cache::forget($this->key($phone));

            return false;
        }

        if (! hash_equals((string) $data['otp'], (string) $otp)) {
            Cache::put($this->key($phone), $data, self::TTL);

            return false;
        }

        Cache::forget($this->key($phone));

        return true;
    }

    public function markVerified(string $phone): void
    {
        Cache::put($this->verifiedKey($phone), true, self::TTL);
    }

    public function isVerified(string $phone): bool
    {
        return (bool) Cache::get($this->verifiedKey($phone));
    }

    public function debugOtp(string $phone): ?string
    {
        $data = Cache::get($this->key($phone));

        return $data['otp'] ?? null;
    }

    private function key(string $phone): string
    {
        return 'otp:' . Str::lower($phone);
    }

    private function cooldownKey(string $phone): string
    {
        return 'otp:cooldown:' . Str::lower($phone);
    }

    private function verifiedKey(string $phone): string
    {
        return 'otp:verified:' . Str::lower($phone);
    }
}
