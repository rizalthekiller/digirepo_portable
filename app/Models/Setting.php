<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['setting_key', 'setting_value'];

    /**
     * Ambil nilai setting berdasarkan key.
     */
    public static function get(string $key, string $default = ''): string
    {
        return static::where('setting_key', $key)->value('setting_value') ?? $default;
    }

    /**
     * Simpan atau update nilai setting.
     */
    public static function set(string $key, ?string $value): void
    {
        static::updateOrCreate(
            ['setting_key' => $key],
            ['setting_value' => trim($value ?? '')]
        );
    }
}
