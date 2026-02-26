<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;

class Setting extends Model
{
    protected $fillable = [
        'group', 'key', 'value', 'type', 'description', 'is_public',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    // Cache key prefix
    protected const CACHE_PREFIX = 'settings_';
    protected const CACHE_TTL = 3600; // 1 hour

    // Scopes
    public function scopeGroup($query, string $group)
    {
        return $query->where('group', $group);
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    // Static Methods
    public static function getValue(string $group, string $key, $default = null)
    {
        $cacheKey = self::CACHE_PREFIX . $group . '_' . $key;

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($group, $key, $default) {
            $setting = self::where('group', $group)->where('key', $key)->first();

            if (!$setting) return $default;

            return self::castValue($setting->value, $setting->type);
        });
    }

    public static function setValue(string $group, string $key, $value): void
    {
        $setting = self::firstOrNew(['group' => $group, 'key' => $key]);

        // Encrypt if needed
        if ($setting->type === 'encrypted' && $value) {
            $value = Crypt::encryptString($value);
        }

        $setting->value = is_array($value) ? json_encode($value) : (string) $value;
        $setting->save();

        // Clear cache
        Cache::forget(self::CACHE_PREFIX . $group . '_' . $key);
    }

    public static function getGroup(string $group): array
    {
        $settings = self::where('group', $group)->get();

        $result = [];
        foreach ($settings as $setting) {
            $result[$setting->key] = self::castValue($setting->value, $setting->type);
        }

        return $result;
    }

    public static function getPublicSettings(): array
    {
        return self::public()->get()
            ->mapWithKeys(fn($s) => [$s->group . '.' . $s->key => self::castValue($s->value, $s->type)])
            ->toArray();
    }

    protected static function castValue($value, string $type)
    {
        return match ($type) {
            'bool' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'json' => json_decode($value, true),
            'encrypted' => $value ? Crypt::decryptString($value) : null,
            'int' => (int) $value,
            'float' => (float) $value,
            default => $value,
        };
    }

    // Helper methods for common settings
    public static function isKlaviyoEnabled(): bool
    {
        return (bool) self::getValue('integrations', 'klaviyo_enabled', false);
    }

    public static function getKlaviyoPublicKey(): ?string
    {
        return self::getValue('integrations', 'klaviyo_public_key');
    }

    public static function getKlaviyoPrivateKey(): ?string
    {
        return self::getValue('integrations', 'klaviyo_private_key');
    }

    public static function getEngagementPoints(string $action): int
    {
        return (int) self::getValue('scoring', 'points_' . $action, 0);
    }

    public static function isMaintenanceEnabled(): bool
    {
        return (bool) self::getValue('general', 'maintenance_enabled', false);
    }
}
