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

    public function mataPelajaranAsGuru()
    {
        return $this->belongsToMany(MataPelajaran::class, 'guru_mata_pelajaran', 'pengguna_id', 'mata_pelajaran_id')
            ->withTimestamps();
    }

    public function isAdmin(): bool
    {
        return $this->peran === 'admin';
    }

    public function isGuruMapel(): bool
    {
        return $this->peran === 'guru';
    }

    public function isStaff(): bool
    {
        return $this->isAdmin() || $this->isGuruMapel();
    }

    /**
     * @return array<int>
     */
    public function assignedMataPelajaranIds(): array
    {
        if (!$this->isGuruMapel()) {
            return [];
        }

        return $this->mataPelajaranAsGuru()
            ->pluck('mata_pelajaran_id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    public function canAccessMataPelajaran(?int $mataPelajaranId): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        if (!$mataPelajaranId) {
            return false;
        }

        return in_array($mataPelajaranId, $this->assignedMataPelajaranIds(), true);
    }

    public function syncMataPelajaranAsGuru(array $mataPelajaranIds): void
    {
        $this->mataPelajaranAsGuru()->sync($mataPelajaranIds);
    }
}
