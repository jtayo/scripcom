<?php

namespace App\Models;

use App\Enums\HotspotStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Hotspot extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'organization_id',
        'router_id',
        'name',
        'slug',
        'ssid',
        'device_model',
        'firmware_version',
        'ip_address',
        'mac_address',
        'isp',
        'bandwidth_up',
        'bandwidth_down',
        'latitude',
        'longitude',
        'ward',
        'sub_county',
        'status',
        'last_seen_at',
        'last_online_at',
        'max_clients',
        'is_active',
        'configuration',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'last_seen_at' => 'datetime',
            'last_online_at' => 'datetime',
            'is_active' => 'boolean',
            'configuration' => 'array',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeNearby(Builder $query, float $lat, float $lng, int $radiusKm = 20): Builder
    {
        $haversine = "(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude))))";

        return $query
            ->select('*')
            ->selectRaw("{$haversine} AS distance", [$lat, $lng, $lat])
            ->having('distance', '<=', $radiusKm)
            ->orderBy('distance');
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function campaigns(): BelongsToMany
    {
        return $this->belongsToMany(Campaign::class)
            ->withPivot('router_id')
            ->withTimestamps();
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(WifiSession::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    public function status(): HotspotStatus
    {
        return HotspotStatus::from($this->status ?? 'offline');
    }
}
