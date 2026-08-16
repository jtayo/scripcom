<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Voucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'sponsor_id',
        'sponsorship_id',
        'hotspot_id',
        'package_id',
        'session_id',
        'code',
        'batch_id',
        'type',
        'value',
        'max_uses',
        'used_count',
        'status',
        'created_by',
        'expires_at',
        'redeemed_phone',
        'redeemed_at',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'redeemed_at' => 'datetime',
            'max_uses' => 'integer',
            'used_count' => 'integer',
        ];
    }

    public function scopeUnused(Builder $query): Builder
    {
        return $query->where('status', 'unused');
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    public function isRedeemable(): bool
    {
        if ($this->status === 'revoked' || $this->status === 'used') {
            return false;
        }

        if ($this->status === 'unused' && $this->max_uses !== null && $this->used_count >= $this->max_uses) {
            return false;
        }

        return ! $this->isExpired();
    }

    public function sponsor(): BelongsTo
    {
        return $this->belongsTo(Sponsor::class);
    }

    public function sponsorship(): BelongsTo
    {
        return $this->belongsTo(Sponsorship::class);
    }

    public function hotspot(): BelongsTo
    {
        return $this->belongsTo(Hotspot::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(WifiPackage::class);
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(WifiSession::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
