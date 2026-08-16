<?php

namespace App\Models;

use App\Enums\SessionStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WifiSession extends Model
{
    use HasFactory;

    protected $table = 'wifi_sessions';

    protected $fillable = [
        'session_id',
        'organization_id',
        'hotspot_id',
        'campaign_id',
        'package_id',
        'sponsorship_id',
        'provider_session_id',
        'phone',
        'mac_address',
        'device_type',
        'browser',
        'ip_address',
        'auth_method',
        'video_completed',
        'video_watch_duration',
        'total_duration',
        'bandwidth_used',
        'bandwidth_up',
        'bandwidth_down',
        'session_started_at',
        'expires_at',
        'ended_at',
        'last_heartbeat_at',
        'status',
        'end_reason',
    ];

    protected function casts(): array
    {
        return [
            'session_started_at' => 'datetime',
            'expires_at' => 'datetime',
            'ended_at' => 'datetime',
            'last_heartbeat_at' => 'datetime',
            'video_completed' => 'boolean',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', SessionStatus::Active->value);
    }

    public function scopeExpired(Builder $query): Builder
    {
        return $query
            ->where('status', SessionStatus::Active->value)
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now());
    }

    public function isExpired(): bool
    {
        return $this->status === SessionStatus::Active->value
            && $this->expires_at !== null
            && $this->expires_at->isPast();
    }

    public function remainingSeconds(): int
    {
        if ($this->expires_at === null) {
            return 0;
        }

        return max(0, (int) round(now()->diffInSeconds($this->expires_at)));
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
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

    public function package(): BelongsTo
    {
        return $this->belongsTo(WifiPackage::class);
    }

    public function statusObject(): SessionStatus
    {
        return SessionStatus::from($this->status ?? 'active');
    }

    public function durationHours(): float
    {
        return round($this->total_duration / 3600, 2);
    }

    public function bandwidthMb(): float
    {
        return round($this->bandwidth_used / (1024 * 1024), 2);
    }
}
