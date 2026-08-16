<?php

namespace App\Models;

use App\Enums\RevenueSource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RevenueRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'wifi_session_id',
        'hotspot_id',
        'campaign_id',
        'sponsorship_id',
        'payment_id',
        'invoice_id',
        'package_id',
        'source',
        'description',
        'gross_amount',
        'payment_fee',
        'net_amount',
        'currency',
        'revenue_date',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'gross_amount' => 'decimal:2',
            'payment_fee' => 'decimal:2',
            'net_amount' => 'decimal:2',
            'revenue_date' => 'date',
            'metadata' => 'array',
        ];
    }

    public function scopeForPeriod(Builder $query, ?string $from = null, ?string $to = null): Builder
    {
        if ($from) {
            $query->where('revenue_date', '>=', $from);
        }

        if ($to) {
            $query->where('revenue_date', '<=', $to);
        }

        return $query;
    }

    public function sourceObject(): RevenueSource
    {
        return RevenueSource::tryFrom($this->source) ?? RevenueSource::Advertising;
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function wifiSession(): BelongsTo
    {
        return $this->belongsTo(WifiSession::class);
    }

    public function hotspot(): BelongsTo
    {
        return $this->belongsTo(Hotspot::class);
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function sponsorship(): BelongsTo
    {
        return $this->belongsTo(Sponsorship::class);
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(WifiPackage::class);
    }
}
