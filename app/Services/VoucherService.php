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
        ?int $expiresAt = null,
        ?int $packageId = null,
        ?int $hotspotId = null,
        ?int $maxUses = null
    ): array {
        $count = min($count, 1000);
        $batchId = 'V-'.strtoupper(Str::random(8));

        $expiresAt = $expiresAt !== null ? date('Y-m-d H:i:s', $expiresAt) : null;

        $codes = [];

        for ($i = 0; $i < $count; $i++) {
            $codes[] = [
                'sponsor_id' => $sponsorId,
                'sponsorship_id' => $sponsorshipId,
                'hotspot_id' => $hotspotId,
                'package_id' => $packageId,
                'code' => $this->uniqueCode(),
                'batch_id' => $batchId,
                'type' => $type,
                'value' => $value,
                'max_uses' => $maxUses,
                'used_count' => 0,
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

        if (! $voucher->isRedeemable()) {
            if ($voucher->status === 'used' || ($voucher->max_uses !== null && $voucher->used_count >= $voucher->max_uses)) {
                return ['success' => false, 'message' => 'Voucher already used'];
            }

            if ($voucher->isExpired()) {
                return ['success' => false, 'message' => 'Voucher has expired'];
            }

            return ['success' => false, 'message' => 'Voucher is no longer valid'];
        }

        $voucher->update([
            'status' => $voucher->max_uses !== null && $voucher->used_count + 1 >= $voucher->max_uses ? 'used' : 'unused',
            'used_count' => $voucher->used_count + 1,
            'redeemed_phone' => $phone,
            'redeemed_at' => now(),
            'hotspot_id' => $hotspotId ?: $voucher->hotspot_id,
        ]);

        return [
            'success' => true,
            'message' => 'Voucher redeemed',
            'value' => $voucher->value,
            'type' => $voucher->type,
            'package_id' => $voucher->package_id,
            'voucher' => $voucher->fresh(),
        ];
    }

    private function uniqueCode(): string
    {
        do {
            $code = strtoupper(Str::random(10));
        } while (Voucher::where('code', $code)->exists());

        return $code;
    }
}
