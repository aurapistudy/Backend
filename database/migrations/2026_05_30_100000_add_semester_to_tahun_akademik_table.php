<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('tahun_akademik', 'semester')) {
            Schema::table('tahun_akademik', function (Blueprint $table) {
                $table->string('semester', 10)->default('ganjil')->after('nama');
            });
        }

        DB::table('tahun_akademik')
            ->whereNull('semester')
            ->orWhere('semester', '')
            ->update(['semester' => 'ganjil']);

        Schema::table('tahun_akademik', function (Blueprint $table) {
            $table->dropUnique(['nama']);
            $table->unique(['nama', 'semester']);
        });
    }

    public function down(): void
    {
        if (!Schema::hasColumn('tahun_akademik', 'semester')) {
            return;
        }

        Schema::table('tahun_akademik', function (Blueprint $table) {
            $table->dropUnique(['nama', 'semester']);
            $table->unique('nama');
            $table->dropColumn('semester');
        });
    }
};
