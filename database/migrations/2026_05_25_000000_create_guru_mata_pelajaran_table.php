<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Bersihkan tabel sisa deploy gagal (biasanya masih kosong).
        Schema::dropIfExists('guru_mata_pelajaran');

        Schema::create('guru_mata_pelajaran', function (Blueprint $table) {
            $table->id();
            // Tanpa foreign key agar kompatibel lokal (bigint signed) dan Railway (bigint unsigned).
            $table->unsignedBigInteger('pengguna_id');
            $table->unsignedBigInteger('mata_pelajaran_id');
            $table->timestamps();

            $table->unique(['pengguna_id', 'mata_pelajaran_id']);
            $table->index('pengguna_id');
            $table->index('mata_pelajaran_id');
        });

        if (Schema::hasTable('pengguna')) {
            DB::table('pengguna')
                ->where('email', 'superadmin@ruma.com')
                ->update(['peran' => 'admin']);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('guru_mata_pelajaran');

        if (Schema::hasTable('pengguna')) {
            DB::table('pengguna')
                ->where('email', 'superadmin@ruma.com')
                ->update(['peran' => 'guru']);
        }
    }
};
