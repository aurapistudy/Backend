<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;

class Materi extends Model
{
    protected $table = 'materi';

    protected $fillable = [
        'judul',
        'deskripsi',
        'mata_pelajaran_id',
        'level_id',
        'semester',
        'tipe_konten',
        'konten_teks',
        'file_path',
        'cover_path',
        'jumlah_halaman',
        'pdf_page_selection',
        'status_aktif',
        'dibuat_oleh',
    ];

    protected $casts = [
        'status_aktif' => 'boolean',
        'jumlah_halaman' => 'integer',
    ];

    protected $appends = [
        'file_url',
        'cover_url',
    ];

    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'dibuat_oleh');
    }

    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class, 'mata_pelajaran_id');
    }

    public function level()
    {
        return $this->belongsTo(Level::class, 'level_id');
    }

    public function kuis()
    {
        return $this->hasMany(Kuis::class, 'materi_id');
    }

    public function bab()
    {
        return $this->hasMany(MateriBab::class, 'materi_id')->orderBy('urutan');
    }

    public function getFileUrlAttribute(): ?string
    {
        if (!$this->file_path) {
            return null;
        }

        return URL::route('media.public.show', ['path' => $this->file_path], true);
    }

    public function getCoverUrlAttribute(): ?string
    {
        if (!$this->cover_path) {
            return null;
        }

        return URL::route('media.public.show', ['path' => $this->cover_path], true);
    }
}
