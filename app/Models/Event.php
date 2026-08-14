<?php

namespace App\Models;

use App\Enums\EventType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Event extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'organization_id',
        'session_id',
        'hotspot_id',
        'campaign_id',
        'event_type',
        'payload',
        'ip_address',
        'user_agent',
        'occurred_at',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'occurred_at' => 'datetime',
        ];
    }

    public function scopeByType(Builder $query, string $type): Builder
    {
        return $query->where('event_type', $type);
    }

    public function scopeForPeriod(Builder $query, $from, $to): Builder
    {
        return $query->whereBetween('occurred_at', [$from, $to]);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function session(): BelongsTo
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

    public function eventType(): EventType
    {
        return EventType::tryFrom($this->event_type) ?? EventType::ErrorOccurred;
    }
}
