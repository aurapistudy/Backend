<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    /**
     * Ambil value setting berdasarkan key.
     * Kalau belum ada di database, akan return $default.
     */
    public static function get(string $key, $default = null)
    {
        return Cache::rememberForever("setting.$key", function () use ($key, $default) {
            $row = static::where('key', $key)->first();

            if (!$row || $row->value === null) {
                return $default;
            }

            return decrypt($row->value);
        });
    }

    /**
     * Simpan/update value setting berdasarkan key.
     */
    public static function set(string $key, $value): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => encrypt($value)]
        );

        Cache::forget("setting.$key");
    }
}