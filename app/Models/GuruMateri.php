<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GuruMateri extends Model
{
    protected $table = 'guru_materi';

    protected $fillable = [
        'pengguna_id',
        'materi_id',
        'tahun_akademik_id',
    ];

    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'pengguna_id');
    }

    public function materi()
    {
        return $this->belongsTo(Materi::class, 'materi_id');
    }

    public function tahunAkademik()
    {
        return $this->belongsTo(TahunAkademik::class, 'tahun_akademik_id');
    }
}
