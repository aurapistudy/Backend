<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Materi;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait FiltersByAssignedMapel
{
    /**
     * ID materi yang boleh diakses guru mapel. null = semua (admin).
     *
     * @return array<int>|null
     */
    protected function staffMateriIds(): ?array
    {
        $user = Auth::user();

        if (!$user || $user->isAdmin()) {
            return null;
        }

        if ($user->isGuruMapel()) {
            return $user->assignedMateriIds();
        }

        return [];
    }

    protected function applyMapelFilterToMateri(Builder $query): Builder
    {
        $ids = $this->staffMateriIds();

        if ($ids === null) {
            return $query;
        }

        if ($ids === []) {
            return $query->whereRaw('0 = 1');
        }

        return $query->whereIn('id', $ids);
    }

    protected function applyMapelFilterToKuis(Builder $query): Builder
    {
        $ids = $this->staffMateriIds();

        if ($ids === null) {
            return $query;
        }

        if ($ids === []) {
            return $query->whereRaw('0 = 1');
        }

        return $query->whereIn('materi_id', $ids);
    }

    protected function applyMapelFilterToMataPelajaran(Builder $query): Builder
    {
        $materiIds = $this->staffMateriIds();

        if ($materiIds === null) {
            return $query;
        }

        if ($materiIds === []) {
            return $query->whereRaw('0 = 1');
        }

        $mapelIds = Materi::query()
            ->whereIn('id', $materiIds)
            ->whereNotNull('mata_pelajaran_id')
            ->pluck('mata_pelajaran_id')
            ->unique()
            ->all();

        if ($mapelIds === []) {
            return $query->whereRaw('0 = 1');
        }

        return $query->whereIn('id', $mapelIds);
    }

    protected function authorizeMateriAccess(Materi $materi): void
    {
        $user = Auth::user();

        if (!$user || !$user->isStaff()) {
            abort(403);
        }

        if ($user->isAdmin() || $user->canAccessMateri($materi->id)) {
            return;
        }

        abort(403, 'Anda tidak memiliki akses ke mata pelajaran ini.');
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

        if (!$mataPelajaranId) {
            abort(403, 'Anda tidak memiliki akses ke mata pelajaran ini.');
        }

        $allowed = Materi::query()
            ->whereIn('id', $user->assignedMateriIds())
            ->where('mata_pelajaran_id', $mataPelajaranId)
            ->exists();

        if ($allowed) {
            return;
        }

        abort(403, 'Anda tidak memiliki akses ke mata pelajaran ini.');
    }

    protected function applyMapelFilterToKuisHasil(Builder $query): Builder
    {
        $ids = $this->staffMateriIds();

        if ($ids === null) {
            return $query;
        }

        if ($ids === []) {
            return $query->whereRaw('0 = 1');
        }

        return $query->whereHas('kuis', function (Builder $kuisQuery) use ($ids) {
            $kuisQuery->whereIn('materi_id', $ids);
        });
    }
}
