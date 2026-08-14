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
        'session_id',
        'code',
        'batch_id',
        'type',
        'value',
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

    public function session(): BelongsTo
    {
        return $this->belongsTo(WifiSession::class);
    }
}
