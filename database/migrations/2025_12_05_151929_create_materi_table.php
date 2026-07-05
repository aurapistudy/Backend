<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('materi', function (Blueprint $table) {
            $table->id();
            $table->string('judul', 200);
            $table->text('deskripsi')->nullable();
            $table->string('mata_pelajaran', 100)->nullable();
            $table->string('level', 50)->nullable();
            $table->string('tipe_konten', 20)->comment('teks / file'); // teks / file
            $table->text('konten_teks')->nullable();
            $table->string('file_path', 255)->nullable();
            $table->integer('jumlah_halaman')->nullable();
            $table->boolean('status_aktif')->default(true);
            $table->unsignedBigInteger('dibuat_oleh')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materi');
    }
};
