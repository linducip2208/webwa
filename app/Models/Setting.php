<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * Penyimpanan setting global key/value yang ringan (cached).
 *
 *   Setting::get('whatsapp.daemon_enabled', true);
 *   Setting::set('whatsapp.daemon_enabled', '1');
 */
#[Fillable(['key', 'value'])]
class Setting extends Model
{
    protected static function cacheKey(string $key): string
    {
        return 'setting:'.$key;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $value = Cache::rememberForever(static::cacheKey($key), function () use ($key) {
            return static::query()->where('key', $key)->value('value');
        });

        return $value ?? $default;
    }

    public static function set(string $key, mixed $value): void
    {
        static::query()->updateOrCreate(
            ['key' => $key],
            ['value' => $value === null ? null : (string) $value],
        );

        Cache::forget(static::cacheKey($key));
    }

    public static function forget(string $key): void
    {
        static::query()->where('key', $key)->delete();
        Cache::forget(static::cacheKey($key));
    }
}
