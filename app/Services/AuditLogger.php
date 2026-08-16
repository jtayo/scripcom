<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;

class AuditLogger
{
    /**
     * Sensitive attributes never persisted to the audit trail.
     */
    private const SENSITIVE = [
        'password',
        'remember_token',
        'encrypted_password',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    public static function record(
        string $action,
        ?string $entityType = null,
        ?int $entityId = null,
        ?string $entityLabel = null,
        ?array $oldValues = null,
        ?array $newValues = null
    ): void {
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'entity_label' => $entityLabel,
            'old_values' => self::sanitize($oldValues),
            'new_values' => self::sanitize($newValues),
            'ip_address' => request()->ip(),
            'user_agent' => substr((string) request()->userAgent(), 0, 500),
        ]);
    }

    public static function model(string $action, Model $model, ?array $oldValues = null, ?array $newValues = null): void
    {
        self::record(
            $action,
            get_class($model),
            $model->getKey() ? (int) $model->getKey() : null,
            self::labelFor($model),
            $oldValues,
            $newValues ?? $model->getAttributes(),
        );
    }

    private static function labelFor(Model $model): ?string
    {
        $attributes = $model->getAttributes();

        foreach (['name', 'title', 'code', 'reference', 'invoice_number', 'contract_number', 'session_id'] as $key) {
            if (array_key_exists($key, $attributes) && $attributes[$key] !== null) {
                return (string) $attributes[$key];
            }
        }

        return '#'.($model->getKey() ?? '');
    }

    private static function sanitize(?array $values): ?array
    {
        if ($values === null) {
            return null;
        }

        $values = array_filter($values, fn ($key) => ! in_array($key, self::SENSITIVE, true), ARRAY_FILTER_USE_KEY);

        foreach ($values as &$value) {
            if (is_array($value)) {
                $value = self::sanitize($value);
            }
        }

        return $values;
    }
}
