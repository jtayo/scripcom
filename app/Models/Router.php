<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Router extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'organization_id',
        'hotspot_id',
        'name',
        'model',
        'firmware_version',
        'ip_address',
        'port',
        'username',
        'password',
        'status',
        'last_seen_at',
        'last_online_at',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'port' => 'integer',
            'password' => 'encrypted',
            'last_seen_at' => 'datetime',
            'last_online_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function hotspot(): BelongsTo
    {
        return $this->belongsTo(Hotspot::class);
    }

    public function healthLogs(): HasMany
    {
        return $this->hasMany(RouterHealthLog::class)->orderByDesc('recorded_at');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOnline(Builder $query): Builder
    {
        return $query->where('status', 'online');
    }

    public function latestHealth(): ?RouterHealthLog
    {
        return $this->healthLogs()->first();
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            'online' => 'success',
            'degraded' => 'warning',
            'offline' => 'danger',
            default => 'secondary',
        };
    }
}
