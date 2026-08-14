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
        'ended_at',
        'last_heartbeat_at',
        'status',
        'end_reason',
    ];

    protected function casts(): array
    {
        return [
            'session_started_at' => 'datetime',
            'ended_at' => 'datetime',
            'last_heartbeat_at' => 'datetime',
            'video_completed' => 'boolean',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', SessionStatus::Active->value);
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
