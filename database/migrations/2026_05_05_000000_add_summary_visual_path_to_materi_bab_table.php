<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('materi_bab', function (Blueprint $table) {
            $table->string('summary_visual_path')->nullable()->after('summary_example');
        });
    }

    public function down(): void
    {
        Schema::table('materi_bab', function (Blueprint $table) {
            $table->dropColumn('summary_visual_path');
        });
    }
};
