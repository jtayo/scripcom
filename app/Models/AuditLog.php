<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    use HasFactory;

    public const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'action',
        'entity_type',
        'entity_id',
        'entity_label',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'old_values' => 'array',
            'new_values' => 'array',
        ];
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (! $term) {
            return $query;
        }

        $query->where(function (Builder $q) use ($term) {
            $q->where('action', 'like', "%{$term}%")
                ->orWhere('entity_type', 'like', "%{$term}%")
                ->orWhere('entity_label', 'like', "%{$term}%")
                ->orWhere('ip_address', 'like', "%{$term}%")
                ->orWhere('user_agent', 'like', "%{$term}%");
        });

        return $query;
    }

    public function actionLabel(): string
    {
        return ucfirst($this->action);
    }

    public function entityLabel(): string
    {
        if ($this->entity_label) {
            return $this->entity_label;
        }

        return str($this->entity_type ?? 'system')->afterLast('\\')->replace('_', ' ')->title();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }
}
