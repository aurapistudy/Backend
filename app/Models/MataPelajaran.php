<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MataPelajaran extends Model
{
    protected $table = 'mata_pelajaran';

    protected $fillable = [
        'nama',
        'deskripsi',
        'status_aktif',
    ];

    protected $casts = [
        'status_aktif' => 'boolean',
    ];

    public function materi()
    {
        return $this->hasMany(Materi::class, 'mata_pelajaran_id');
    }

    public function guruPengguna()
    {
        return $this->belongsToMany(Pengguna::class, 'guru_mata_pelajaran', 'mata_pelajaran_id', 'pengguna_id')
            ->withTimestamps();
    }
}
