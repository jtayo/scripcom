<?php

namespace App\Services;

use App\Models\Voucher;
use Illuminate\Support\Str;

class VoucherService
{
    public function generateBatch(
        string $type,
        int $value,
        int $count,
        ?int $sponsorId = null,
        ?int $sponsorshipId = null,
        ?int $expiresAt = null
    ): array {
        $count = min($count, 1000);
        $batchId = 'V-' . strtoupper(Str::random(8));

        $codes = [];

        for ($i = 0; $i < $count; $i++) {
            $codes[] = [
                'sponsor_id' => $sponsorId,
                'sponsorship_id' => $sponsorshipId,
                'code' => $this->uniqueCode(),
                'batch_id' => $batchId,
                'type' => $type,
                'value' => $value,
                'status' => 'unused',
                'created_by' => auth()->id(),
                'expires_at' => $expiresAt,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        foreach (array_chunk($codes, 500) as $chunk) {
            Voucher::insert($chunk);
        }

        return ['batch_id' => $batchId, 'count' => $count];
    }

    public function redeem(string $code, ?string $phone = null, ?int $hotspotId = null): array
    {
        $voucher = Voucher::query()->where('code', $code)->first();

        if (! $voucher) {
            return ['success' => false, 'message' => 'Invalid voucher code'];
        }

        if ($voucher->status !== 'unused') {
            return ['success' => false, 'message' => 'Voucher already used'];
        }

        if ($voucher->isExpired()) {
            return ['success' => false, 'message' => 'Voucher has expired'];
        }

        $voucher->update([
            'status' => 'used',
            'redeemed_phone' => $phone,
            'redeemed_at' => now(),
            'hotspot_id' => $hotspotId,
        ]);

        return ['success' => true, 'message' => 'Voucher redeemed', 'value' => $voucher->value];
    }

    private function uniqueCode(): string
    {
        do {
            $code = strtoupper(Str::random(10));
        } while (Voucher::where('code', $code)->exists());

        return $code;
    }
}
