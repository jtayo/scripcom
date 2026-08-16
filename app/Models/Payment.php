<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'sponsorship_id',
        'package_id',
        'hotspot_id',
        'wifi_session_id',
        'phone',
        'amount',
        'currency',
        'status',
        'checkout_request_id',
        'mpesa_receipt_number',
        'transaction_id',
        'result_code',
        'result_description',
        'callback_payload',
        'transacted_at',
    ];

    protected function casts(): array
    {
        return [
            'callback_payload' => 'array',
            'transacted_at' => 'datetime',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function sponsorship(): BelongsTo
    {
        return $this->belongsTo(Sponsorship::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(WifiPackage::class);
    }

    public function hotspot(): BelongsTo
    {
        return $this->belongsTo(Hotspot::class);
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(WifiSession::class, 'wifi_session_id');
    }
}
