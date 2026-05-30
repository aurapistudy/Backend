<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('guru_materi')) {
            return;
        }

        if (!Schema::hasColumn('guru_materi', 'tahun_akademik_id')) {
            Schema::table('guru_materi', function (Blueprint $table) {
                $table->unsignedBigInteger('tahun_akademik_id')->nullable()->after('materi_id');
                $table->index('tahun_akademik_id');
            });
        }

        $defaultTahunId = DB::table('tahun_akademik')
            ->where('status_aktif', true)
            ->value('id');

        if (!$defaultTahunId) {
            $defaultTahunId = DB::table('tahun_akademik')->orderByDesc('id')->value('id');
        }

        if (!$defaultTahunId && Schema::hasTable('tahun_akademik')) {
            $defaultTahunId = DB::table('tahun_akademik')->insertGetId([
                'nama' => '2025/2026',
                'tanggal_mulai' => '2025-07-01',
                'tanggal_selesai' => '2026-06-30',
                'status_aktif' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        if ($defaultTahunId) {
            DB::table('guru_materi')
                ->whereNull('tahun_akademik_id')
                ->update(['tahun_akademik_id' => $defaultTahunId]);
        }

        Schema::table('guru_materi', function (Blueprint $table) {
            try {
                $table->dropUnique(['pengguna_id', 'materi_id']);
            } catch (\Throwable) {
                // Index mungkin sudah tidak ada.
            }
        });

        Schema::table('guru_materi', function (Blueprint $table) {
            if (!$this->hasCompositeUnique('guru_materi', ['pengguna_id', 'materi_id', 'tahun_akademik_id'])) {
                $table->unique(['pengguna_id', 'materi_id', 'tahun_akademik_id']);
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('guru_materi') || !Schema::hasColumn('guru_materi', 'tahun_akademik_id')) {
            return;
        }

        Schema::table('guru_materi', function (Blueprint $table) {
            try {
                $table->dropUnique(['pengguna_id', 'materi_id', 'tahun_akademik_id']);
            } catch (\Throwable) {
                //
            }

            $table->dropIndex(['tahun_akademik_id']);
            $table->dropColumn('tahun_akademik_id');
        });

        Schema::table('guru_materi', function (Blueprint $table) {
            $table->unique(['pengguna_id', 'materi_id']);
        });
    }

    private function hasCompositeUnique(string $table, array $columns): bool
    {
        $indexes = Schema::getIndexes($table);

        foreach ($indexes as $index) {
            if (($index['unique'] ?? false) && ($index['columns'] ?? []) === $columns) {
                return true;
            }
        }

        return false;
    }
};
