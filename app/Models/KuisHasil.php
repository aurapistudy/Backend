<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KuisHasil extends Model
{
    protected $table = 'kuis_hasil';

    protected $fillable = [
        'kuis_id',
        'pengguna_id',
        'skor',
        'total_benar',
        'total_pertanyaan',
        'selesai_at',
    ];

    protected $casts = [
        'skor' => 'integer',
        'total_benar' => 'integer',
        'total_pertanyaan' => 'integer',
        'selesai_at' => 'datetime',
    ];

    public function kuis()
    {
        return $this->belongsTo(Kuis::class, 'kuis_id');
    }

    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'pengguna_id');
    }

    public function scopeWithPendingFlag($query)
    {
        return $query->withExists(['jawaban as has_pending' => function ($inner) {
            $inner->where('status_koreksi', 'pending');
        }]);
    }

    public function jawaban()
    {
        return $this->hasMany(KuisJawaban::class, 'kuis_hasil_id');
    }
}