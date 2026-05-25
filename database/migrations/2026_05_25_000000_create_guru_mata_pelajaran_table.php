<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('guru_mata_pelajaran');

        Schema::create('guru_mata_pelajaran', function (Blueprint $table) {
            $table->id();
            // Sesuaikan tipe id parent: pengguna = bigint signed, mata_pelajaran = bigint unsigned.
            $table->bigInteger('pengguna_id');
            $table->unsignedBigInteger('mata_pelajaran_id');
            $table->timestamps();

            $table->unique(['pengguna_id', 'mata_pelajaran_id']);
            $table->foreign('pengguna_id')
                ->references('id')
                ->on('pengguna')
                ->cascadeOnDelete();
            $table->foreign('mata_pelajaran_id')
                ->references('id')
                ->on('mata_pelajaran')
                ->cascadeOnDelete();
        });

        DB::table('pengguna')
            ->where('email', 'superadmin@ruma.com')
            ->update(['peran' => 'admin']);
    }

    public function down(): void
    {
        Schema::dropIfExists('guru_mata_pelajaran');

        DB::table('pengguna')
            ->where('email', 'superadmin@ruma.com')
            ->update(['peran' => 'guru']);
    }
};
