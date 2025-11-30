<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rescuers', function (Blueprint $table) {
            if (!Schema::hasColumn('rescuers', 'motivo_postulacion')) {
                $table->text('motivo_postulacion')->nullable()->after('cv_documentado');
            }
        });

        Schema::table('veterinarians', function (Blueprint $table) {
            if (!Schema::hasColumn('veterinarians', 'motivo_postulacion')) {
                $table->text('motivo_postulacion')->nullable()->after('cv_documentado');
            }
        });
    }

    public function down(): void
    {
        Schema::table('rescuers', function (Blueprint $table) {
            if (Schema::hasColumn('rescuers', 'motivo_postulacion')) {
                $table->dropColumn('motivo_postulacion');
            }
        });

        Schema::table('veterinarians', function (Blueprint $table) {
            if (Schema::hasColumn('veterinarians', 'motivo_postulacion')) {
                $table->dropColumn('motivo_postulacion');
            }
        });
    }
};


