<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Move any existing values from cv_path -> cv_documentado, then drop cv_path
        if (Schema::hasColumn('rescuers', 'cv_path')) {
            DB::table('rescuers')
                ->whereNull('cv_documentado')
                ->update(['cv_documentado' => DB::raw('cv_path')]);
            Schema::table('rescuers', function (Blueprint $table) {
                $table->dropColumn('cv_path');
            });
        }

        if (Schema::hasColumn('veterinarians', 'cv_path')) {
            DB::table('veterinarians')
                ->whereNull('cv_documentado')
                ->update(['cv_documentado' => DB::raw('cv_path')]);
            Schema::table('veterinarians', function (Blueprint $table) {
                $table->dropColumn('cv_path');
            });
        }
    }

    public function down(): void
    {
        // Recreate cv_path as nullable string (no data back-fill)
        if (!Schema::hasColumn('rescuers', 'cv_path')) {
            Schema::table('rescuers', function (Blueprint $table) {
                $table->string('cv_path')->nullable()->after('cv_documentado');
            });
        }
        if (!Schema::hasColumn('veterinarians', 'cv_path')) {
            Schema::table('veterinarians', function (Blueprint $table) {
                $table->string('cv_path')->nullable()->after('cv_documentado');
            });
        }
    }
};



