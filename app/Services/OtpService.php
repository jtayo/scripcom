<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class OtpService
{
    private const TTL = 600;
    private const MAX_ATTEMPTS = 5;

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

    public function debugOtp(string $phone): ?string
    {
        $data = Cache::get($this->key($phone));

        return $data['otp'] ?? null;
    }

    private function key(string $phone): string
    {
        return 'otp:' . Str::lower($phone);
    }
}
