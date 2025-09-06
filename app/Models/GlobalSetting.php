<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GlobalSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'description',
        'type',
        'is_public',
    ];

    protected $casts = [
        'value' => 'array',
        'is_public' => 'boolean',
    ];

    /**
     * Get a global setting by key
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();
        
        if (!$setting) {
            return $default;
        }

        return $setting->getValue();
    }

    /**
     * Set a global setting
     */
    public static function set(string $key, mixed $value, string $type = 'string', string $description = null): static
    {
        return static::updateOrCreate(
            ['key' => $key],
            [
                'value' => is_array($value) ? $value : [$value],
                'type' => $type,
                'description' => $description,
            ]
        );
    }

    /**
     * Get the value based on type
     */
    public function getValue(): mixed
    {
        $value = $this->value;

        if (!is_array($value)) {
            return $value;
        }

        if (count($value) === 1 && array_is_list($value)) {
            $value = $value[0];
        }

        return match ($this->type) {
            'boolean' => (bool) $value,
            'integer' => (int) $value,
            'string' => (string) $value,
            'array', 'object' => $value,
            default => $value,
        };
    }

    /**
     * Get all public settings for frontend
     */
    public static function getPublicSettings(): array
    {
        return static::where('is_public', true)
            ->select(['key', 'value', 'type'])
            ->get()
            ->mapWithKeys(function ($setting) {
                return [$setting->key => $setting->getValue()];
            })
            ->toArray();
    }

    /**
     * Get settings by prefix
     */
    public static function getByPrefix(string $prefix): array
    {
        return static::where('key', 'like', $prefix . '%')
            ->select(['key', 'value', 'type'])
            ->get()
            ->mapWithKeys(function ($setting) {
                return [$setting->key => $setting->getValue()];
            })
            ->toArray();
    }
}
