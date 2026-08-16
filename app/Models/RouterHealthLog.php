<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RouterHealthLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'router_id',
        'status',
        'cpu_usage',
        'memory_usage',
        'uptime_seconds',
        'rx_bytes',
        'tx_bytes',
        'latency_ms',
        'active_users',
        'recorded_at',
    ];

    protected function casts(): array
    {
        return [
            'cpu_usage' => 'decimal:2',
            'memory_usage' => 'decimal:2',
            'uptime_seconds' => 'integer',
            'rx_bytes' => 'integer',
            'tx_bytes' => 'integer',
            'latency_ms' => 'decimal:2',
            'active_users' => 'integer',
            'recorded_at' => 'datetime',
        ];
    }

    public function router(): BelongsTo
    {
        return $this->belongsTo(Router::class);
    }

    public function uptimeLabel(): string
    {
        $seconds = (int) $this->uptime_seconds;

        if ($seconds <= 0) {
            return '—';
        }

        $days = intdiv($seconds, 86400);
        $hours = intdiv($seconds % 86400, 3600);

        return $days > 0 ? "{$days}d {$hours}h" : "{$hours}h ".intdiv($seconds % 3600, 60).'m';
    }
}
