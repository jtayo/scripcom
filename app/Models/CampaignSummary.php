<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignSummary extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'date',
        'total_plays',
        'completions',
        'avg_watch_duration',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }
}
