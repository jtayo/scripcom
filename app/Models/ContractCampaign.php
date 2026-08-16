<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContractCampaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'contract_id',
        'campaign_id',
        'sessions_allocated',
        'unit_price',
    ];

    protected function casts(): array
    {
        return [
            'sessions_allocated' => 'integer',
            'unit_price' => 'decimal:2',
        ];
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }
}
