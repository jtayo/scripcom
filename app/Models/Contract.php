<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contract extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'organization_id',
        'sponsor_id',
        'contract_number',
        'title',
        'type',
        'status',
        'start_date',
        'end_date',
        'sessions_allocated',
        'unit_price',
        'tax_rate',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'sessions_allocated' => 'integer',
            'unit_price' => 'decimal:2',
            'tax_rate' => 'decimal:2',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function sponsor(): BelongsTo
    {
        return $this->belongsTo(Sponsor::class);
    }

    public function campaigns(): HasMany
    {
        return $this->hasMany(ContractCampaign::class)->with('campaign:id,title,status');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class)->orderByDesc('issue_date');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function typeLabel(): string
    {
        return ucfirst($this->type);
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            'active' => 'success',
            'draft' => 'secondary',
            'completed' => 'primary',
            'cancelled' => 'danger',
            default => 'secondary',
        };
    }

    /**
     * Sessions consumed across the contract's campaigns within the given date range.
     */
    public function sessionsUsed(?string $from = null, ?string $to = null): int
    {
        $campaignIds = $this->campaigns()->pluck('campaign_id');

        if ($campaignIds->isEmpty()) {
            return 0;
        }

        $query = WifiSession::query()->whereIn('campaign_id', $campaignIds);

        if ($from && $to) {
            $query->whereBetween('session_started_at', [$from, $to]);
        }

        return $query->count();
    }

    public function contractValue(): float
    {
        return round($this->sessions_allocated * $this->unit_price, 2);
    }

    public function utilization(): float
    {
        if ($this->sessions_allocated <= 0) {
            return 0.0;
        }

        return round(min(($this->sessionsUsed() / $this->sessions_allocated) * 100, 100.0), 1);
    }
}
