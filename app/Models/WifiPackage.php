<?php

namespace App\Models;

use App\Enums\PackageAccessType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WifiPackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'name',
        'code',
        'description',
        'duration_minutes',
        'price',
        'access_type',
        'bandwidth_down_kbps',
        'bandwidth_up_kbps',
        'data_limit_mb',
        'simultaneous_devices',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'data_limit_mb' => 'integer',
            'simultaneous_devices' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('access_type', $type);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(WifiSession::class, 'package_id');
    }

    public function accessType(): PackageAccessType
    {
        return PackageAccessType::from($this->access_type ?? 'free');
    }

    public function durationLabel(): string
    {
        if ($this->duration_minutes >= 1440) {
            return $this->duration_minutes % 1440 === 0
                ? ($this->duration_minutes / 1440).' day'.($this->duration_minutes / 1440 > 1 ? 's' : '')
                : $this->duration_minutes.' mins';
        }

        if ($this->duration_minutes >= 60) {
            $hours = intdiv($this->duration_minutes, 60);
            $mins = $this->duration_minutes % 60;

            return $hours.' hr'.($hours > 1 ? 's' : '').($mins ? " {$mins} min" : '');
        }

        return "{$this->duration_minutes} min".($this->duration_minutes > 1 ? 's' : '');
    }

    public function priceLabel(): string
    {
        if ($this->accessType() === PackageAccessType::Free) {
            return 'Free';
        }

        return 'KES '.number_format((float) $this->price, 2);
    }
}
