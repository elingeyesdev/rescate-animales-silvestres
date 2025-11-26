<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rescuers', function (Blueprint $table) {
            if (!Schema::hasColumn('rescuers', 'aprobado')) {
                $table->boolean('aprobado')->nullable()->after('cv_documentado');
            }
            if (!Schema::hasColumn('rescuers', 'motivo_revision')) {
                $table->text('motivo_revision')->nullable()->after('aprobado');
            }
        });

        Schema::table('veterinarians', function (Blueprint $table) {
            if (!Schema::hasColumn('veterinarians', 'aprobado')) {
                $table->boolean('aprobado')->nullable()->after('cv_documentado');
            }
            if (!Schema::hasColumn('veterinarians', 'motivo_revision')) {
                $table->text('motivo_revision')->nullable()->after('aprobado');
            }
        });
    }

    public function down(): void
    {
        Schema::table('rescuers', function (Blueprint $table) {
            if (Schema::hasColumn('rescuers', 'motivo_revision')) {
                $table->dropColumn('motivo_revision');
            }
            if (Schema::hasColumn('rescuers', 'aprobado')) {
                $table->dropColumn('aprobado');
            }
        });

        Schema::table('veterinarians', function (Blueprint $table) {
            if (Schema::hasColumn('veterinarians', 'motivo_revision')) {
                $table->dropColumn('motivo_revision');
            }
            if (Schema::hasColumn('veterinarians', 'aprobado')) {
                $table->dropColumn('aprobado');
            }
        });
    }
};



