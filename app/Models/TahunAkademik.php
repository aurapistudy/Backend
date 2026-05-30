<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class TahunAkademik extends Model
{
    protected $table = 'tahun_akademik';

    protected $fillable = [
        'nama',
        'tanggal_mulai',
        'tanggal_selesai',
        'status_aktif',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'status_aktif' => 'boolean',
    ];

    public function penugasanGuru()
    {
        return $this->hasMany(GuruMateri::class, 'tahun_akademik_id');
    }

    public static function active(): ?self
    {
        return Cache::remember('tahun_akademik_aktif', 300, function () {
            return static::query()
                ->where('status_aktif', true)
                ->orderByDesc('tanggal_mulai')
                ->first();
        });
    }

    public static function activeId(): ?int
    {
        $id = static::active()?->id;

        return $id ? (int) $id : null;
    }

    public static function clearActiveCache(): void
    {
        Cache::forget('tahun_akademik_aktif');
    }

    public static function ensureSingleActive(self $tahun): void
    {
        static::query()
            ->where('id', '!=', $tahun->id)
            ->where('status_aktif', true)
            ->update(['status_aktif' => false]);

        static::clearActiveCache();
    }
}
