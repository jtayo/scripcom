<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'key',
        'value',
    ];

    public static function getValue(string $key, $default = null, ?int $organizationId = null)
    {
        $setting = static::query()
            ->where('key', $key)
            ->where('organization_id', $organizationId)
            ->first();

        if (! $setting) {
            return $default;
        }

        $value = $setting->value;

        return match (true) {
            $value === 'true' || $value === 'false' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            is_numeric($value) => $value + 0,
            default => $value,
        };
    }

    public static function setValue(string $key, $value, ?int $organizationId = null): void
    {
        static::updateOrCreate(
            ['key' => $key, 'organization_id' => $organizationId],
            ['value' => is_bool($value) ? ($value ? 'true' : 'false') : (string) $value]
        );
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
