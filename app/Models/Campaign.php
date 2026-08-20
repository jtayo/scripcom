<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Campaign extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'organization_id',
        'created_by',
        'sponsor_id',
        'title',
        'slug',
        'description',
        'type',
        'content_type',
        'content_url',
        'video_caption',
        'thumbnail',
        'redirect_url',
        'duration_seconds',
        'skip_allowed',
        'is_mandatory',
        'priority',
        'starts_at',
        'ends_at',
        'max_plays',
        'current_plays',
        'status',
        'is_active',
        'targeting_rules',
    ];

    protected function casts(): array
    {
        return [
            'skip_allowed' => 'boolean',
            'is_mandatory' => 'boolean',
            'is_active' => 'boolean',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'targeting_rules' => 'array',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->where('status', 'active');
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function sponsor(): BelongsTo
    {
        return $this->belongsTo(Sponsor::class);
    }

    public function hotspots(): BelongsToMany
    {
        return $this->belongsToMany(Hotspot::class)
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

    public function summary(): HasMany
    {
        return $this->hasMany(CampaignSummary::class);
    }
}
