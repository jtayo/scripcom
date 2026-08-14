<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sponsorship extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'sponsor_id',
        'reference',
        'type',
        'quantity_purchased',
        'quantity_used',
        'unit_price',
        'total_amount',
        'currency',
        'status',
        'starts_at',
        'expires_at',
        'notes',
        'terms',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'expires_at' => 'datetime',
            'terms' => 'array',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function getRemainingAttribute(): int
    {
        return max(0, $this->quantity_purchased - $this->quantity_used);
    }

    public function getUtilizationRateAttribute(): float
    {
        if ($this->quantity_purchased <= 0) {
            return 0;
        }

        return round(($this->quantity_used / $this->quantity_purchased) * 100, 1);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function sponsor(): BelongsTo
    {
        return $this->belongsTo(Sponsor::class);
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(WifiSession::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function vouchers(): HasMany
    {
        return $this->hasMany(Voucher::class);
    }
}
