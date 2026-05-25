<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Materi;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait FiltersByAssignedMapel
{
    /**
     * @return array<int>|null null = semua mapel (admin)
     */
    protected function staffMapelIds(): ?array
    {
        $user = Auth::user();

        if (!$user || $user->isAdmin()) {
            return null;
        }

        if ($user->isGuruMapel()) {
            return $user->assignedMataPelajaranIds();
        }

        return [];
    }

    protected function applyMapelFilterToMateri(Builder $query): Builder
    {
        $ids = $this->staffMapelIds();

        if ($ids === null) {
            return $query;
        }

        if ($ids === []) {
            return $query->whereRaw('0 = 1');
        }

        return $query->whereIn('mata_pelajaran_id', $ids);
    }

    protected function applyMapelFilterToKuis(Builder $query): Builder
    {
        $ids = $this->staffMapelIds();

        if ($ids === null) {
            return $query;
        }

        if ($ids === []) {
            return $query->whereRaw('0 = 1');
        }

        return $query->whereHas('materi', function (Builder $materiQuery) use ($ids) {
            $materiQuery->whereIn('mata_pelajaran_id', $ids);
        });
    }

    protected function applyMapelFilterToMataPelajaran(Builder $query): Builder
    {
        $ids = $this->staffMapelIds();

        if ($ids === null) {
            return $query;
        }

        if ($ids === []) {
            return $query->whereRaw('0 = 1');
        }

        return $query->whereIn('id', $ids);
    }

    protected function authorizeMateriAccess(Materi $materi): void
    {
        $this->authorizeMapelAccess($materi->mata_pelajaran_id);
    }

    protected function authorizeMapelAccess(?int $mataPelajaranId): void
    {
        $user = Auth::user();

        if (!$user || !$user->isStaff()) {
            abort(403);
        }

        if ($user->isAdmin()) {
            return;
        }

        if ($mataPelajaranId && $user->canAccessMataPelajaran($mataPelajaranId)) {
            return;
        }

        abort(403, 'Anda tidak memiliki akses ke mata pelajaran ini.');
    }

    protected function applyMapelFilterToKuisHasil(Builder $query): Builder
    {
        $ids = $this->staffMapelIds();

        if ($ids === null) {
            return $query;
        }

        if ($ids === []) {
            return $query->whereRaw('0 = 1');
        }

        return $query->whereHas('kuis', function (Builder $kuisQuery) use ($ids) {
            $kuisQuery->whereHas('materi', function (Builder $materiQuery) use ($ids) {
                $materiQuery->whereIn('mata_pelajaran_id', $ids);
            });
        });
    }
}
