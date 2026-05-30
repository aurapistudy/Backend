<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
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

    /** Materi yang dikelola guru (sesuai daftar di menu Mata Pelajaran). */
    public function materiAsGuru()
    {
        return $this->belongsToMany(Materi::class, 'guru_materi', 'pengguna_id', 'materi_id')
            ->withPivot('tahun_akademik_id')
            ->withTimestamps();
    }

    public function penugasanGuru()
    {
        return $this->hasMany(GuruMateri::class, 'pengguna_id');
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
    public function assignedMateriIds(?int $tahunAkademikId = null): array
    {
        if (!$this->isGuruMapel()) {
            return [];
        }

        $tahunId = $tahunAkademikId ?? TahunAkademik::activeId();
        if (!$tahunId) {
            return [];
        }

        return $this->materiAsGuru()
            ->wherePivot('tahun_akademik_id', $tahunId)
            ->pluck('materi.id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    public function materiAsGuruAktif(): Collection
    {
        $ids = $this->assignedMateriIds();

        if ($ids === []) {
            return collect();
        }

        return Materi::query()
            ->whereIn('id', $ids)
            ->orderBy('judul')
            ->get(['id', 'judul']);
    }

    public function penugasanRiwayatGrouped(): Collection
    {
        if (!$this->isGuruMapel()) {
            return collect();
        }

        return GuruMateri::query()
            ->with(['materi:id,judul', 'tahunAkademik:id,nama,status_aktif,tanggal_mulai'])
            ->where('pengguna_id', $this->id)
            ->get()
            ->sortByDesc(fn (GuruMateri $row) => $row->tahunAkademik?->tanggal_mulai?->timestamp ?? 0)
            ->groupBy(fn (GuruMateri $row) => $row->tahunAkademik?->nama ?? 'Tidak diketahui');
    }

    public function canAccessMateri(?int $materiId): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        if (!$materiId) {
            return false;
        }

        return in_array($materiId, $this->assignedMateriIds(), true);
    }

    public function attachMateriAsGuru(int $materiId, ?int $tahunAkademikId = null): void
    {
        $tahunId = $tahunAkademikId ?? TahunAkademik::activeId();
        if (!$tahunId) {
            return;
        }

        $exists = DB::table('guru_materi')
            ->where('pengguna_id', $this->id)
            ->where('materi_id', $materiId)
            ->where('tahun_akademik_id', $tahunId)
            ->exists();

        if ($exists) {
            return;
        }

        DB::table('guru_materi')->insert([
            'pengguna_id' => $this->id,
            'materi_id' => $materiId,
            'tahun_akademik_id' => $tahunId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function syncMateriAsGuru(array $materiIds, ?int $tahunAkademikId = null): void
    {
        $tahunId = $tahunAkademikId ?? TahunAkademik::activeId();
        if (!$tahunId) {
            return;
        }

        $materiIds = array_values(array_unique(array_map('intval', $materiIds)));

        $query = DB::table('guru_materi')
            ->where('pengguna_id', $this->id)
            ->where('tahun_akademik_id', $tahunId);

        if ($materiIds === []) {
            $query->delete();

            return;
        }

        $query->whereNotIn('materi_id', $materiIds)->delete();

        foreach ($materiIds as $materiId) {
            $this->attachMateriAsGuru($materiId, $tahunId);
        }
    }
}
