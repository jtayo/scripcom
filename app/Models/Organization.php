<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Organization extends Model
{
    use HasFactory, SoftDeletes;

    public const TYPE_SCRIPCOM = 'scripcom';

    public const TYPE_COUNTY = 'county';

    public const TYPE_CORPORATE = 'corporate';

    public const TYPE_NGO = 'ngo';

    public const TYPE_INSTITUTION = 'institution';

    public const TYPE_ADVERTISER = 'advertiser';

    protected $fillable = [
        'name',
        'slug',
        'email',
        'phone',
        'address',
        'city',
        'county',
        'country',
        'postal_code',
        'logo',
        'website',
        'primary_color',
        'secondary_color',
        'type',
        'is_active',
        'settings',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'settings' => 'array',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public static function types(): array
    {
        return [
            self::TYPE_SCRIPCOM => 'SCRIPCOM',
            self::TYPE_COUNTY => 'County',
            self::TYPE_CORPORATE => 'Corporate',
            self::TYPE_NGO => 'NGO',
            self::TYPE_INSTITUTION => 'Institution',
            self::TYPE_ADVERTISER => 'Advertiser',
        ];
    }

    public function isScripcom(): bool
    {
        return $this->type === self::TYPE_SCRIPCOM;
    }

    public function isCounty(): bool
    {
        return $this->type === self::TYPE_COUNTY;
    }

    public function isCorporate(): bool
    {
        return $this->type === self::TYPE_CORPORATE;
    }

    public function isNgo(): bool
    {
        return $this->type === self::TYPE_NGO;
    }

    public function isInstitution(): bool
    {
        return $this->type === self::TYPE_INSTITUTION;
    }

    public function isAdvertiser(): bool
    {
        return $this->type === self::TYPE_ADVERTISER;
    }

    public function typeLabel(): string
    {
        return self::types()[$this->type] ?? ucfirst((string) $this->type);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function hotspots(): HasMany
    {
        return $this->hasMany(Hotspot::class);
    }

    public function campaigns(): HasMany
    {
        return $this->hasMany(Campaign::class);
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(WifiSession::class);
    }

    public function sponsors(): HasMany
    {
        return $this->hasMany(Sponsor::class);
    }

    public function sponsorships(): HasMany
    {
        return $this->hasMany(Sponsorship::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }
}
