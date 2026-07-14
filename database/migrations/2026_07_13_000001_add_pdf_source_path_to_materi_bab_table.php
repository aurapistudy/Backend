<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('materi_bab', function (Blueprint $table) {
            if (!Schema::hasColumn('materi_bab', 'pdf_source_path')) {
                $table->string('pdf_source_path', 255)->nullable()->after('file_path');
            }
        });
    }

    public function down(): void
    {
        Schema::table('materi_bab', function (Blueprint $table) {
            if (Schema::hasColumn('materi_bab', 'pdf_source_path')) {
                $table->dropColumn('pdf_source_path');
            }
        });
    }
};
