<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Pengguna extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'pengguna';

    protected $fillable = [
        'nama',
        'email',
        'kata_sandi',
        'peran',
        'status_aktif',
        'foto_profil',
        'asr_lang',
        'tts_lang',
        'tts_rate',
        'auto_voice_nav',
    ];

    protected $hidden = [
        'kata_sandi',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'kata_sandi' => 'hashed',
            'status_aktif' => 'boolean',
            'auto_voice_nav' => 'boolean',
        ];
    }

    /**
     * Get the password attribute name for authentication.
     */
    public function getAuthPassword()
    {
        return $this->kata_sandi;
    }

    /**
     * Get the siswa record associated with the pengguna.
     */
    public function siswa()
    {
        return $this->hasOne(Siswa::class, 'pengguna_id');
    }

    /**
     * Get the guru record associated with the pengguna.
     */
    public function guru()
    {
        return $this->hasOne(Guru::class, 'pengguna_id');
    }

    public function kuisHasil()
    {
        return $this->hasMany(KuisHasil::class, 'pengguna_id');
    }
}
