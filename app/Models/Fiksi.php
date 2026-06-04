<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fiksi extends Model
{
    protected $table = 'fiksi';

    protected $fillable = [
        'judul_buku',
        'cover_path',
        'penulis',
        'kategori',
        'tahun_terbit',
        'deskripsi',
        'file_path',
        'jumlah_halaman',
        'status_aktif',
        'dibuat_oleh',
    ];

    protected $casts = [
        'status_aktif' => 'boolean',
        'tahun_terbit' => 'integer',
        'jumlah_halaman' => 'integer',
    ];

    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'dibuat_oleh');
    }
}
