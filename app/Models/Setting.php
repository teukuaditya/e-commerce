<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'value', 'group', 'type'];

    public static function getValue(string $key, $default = null)
    {
        $settings = static::allCached();
        return array_key_exists($key, $settings) ? $settings[$key] : $default;
    }

    public static function setValue(string $key, $value, string $group = 'general', string $type = 'text'): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'group' => $group, 'type' => $type]
        );
        Cache::forget('app_settings_all');
    }

    public static function allCached(): array
    {
        return Cache::rememberForever('app_settings_all', function () {
            return static::query()->pluck('value', 'key')->toArray();
        });
    }

    public static function flushCache(): void
    {
        Cache::forget('app_settings_all');
    }
}
