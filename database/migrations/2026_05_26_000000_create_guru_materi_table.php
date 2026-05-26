<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('guru_materi')) {
            return;
        }

        Schema::create('guru_materi', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pengguna_id');
            $table->unsignedBigInteger('materi_id');
            $table->timestamps();

            $table->unique(['pengguna_id', 'materi_id']);
            $table->index('pengguna_id');
            $table->index('materi_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guru_materi');
    }
};
