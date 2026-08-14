<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteSetting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function valueFor(string $key, mixed $default = null): mixed
    {
        return Cache::remember("site-setting.{$key}", 300, fn () => static::query()->where('key', $key)->value('value') ?? $default);
    }

    public static function put(string $key, mixed $value): void
    {
        static::query()->updateOrCreate(['key' => $key], ['value' => is_array($value) ? json_encode($value) : $value]);
        Cache::forget("site-setting.{$key}");
    }

    public static function json(string $key): array
    {
        return json_decode((string) static::valueFor($key, '{}'), true) ?: [];
    }
}
