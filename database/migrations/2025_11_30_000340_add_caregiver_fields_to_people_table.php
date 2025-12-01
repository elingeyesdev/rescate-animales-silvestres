<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('people', function (Blueprint $table) {
            if (!Schema::hasColumn('people', 'cuidador_center_id')) {
                $table->unsignedBigInteger('cuidador_center_id')
                    ->nullable()
                    ->after('es_cuidador');
            }

            if (!Schema::hasColumn('people', 'cuidador_aprobado')) {
                $table->boolean('cuidador_aprobado')->nullable()->after('cuidador_center_id');
            }

            if (!Schema::hasColumn('people', 'cuidador_motivo_revision')) {
                $table->text('cuidador_motivo_revision')->nullable()->after('cuidador_aprobado');
            }
        });
    }

    public function down(): void
    {
        Schema::table('people', function (Blueprint $table) {
            if (Schema::hasColumn('people', 'cuidador_motivo_revision')) {
                $table->dropColumn('cuidador_motivo_revision');
            }
            if (Schema::hasColumn('people', 'cuidador_aprobado')) {
                $table->dropColumn('cuidador_aprobado');
            }
            if (Schema::hasColumn('people', 'cuidador_center_id')) {
                $table->dropColumn('cuidador_center_id');
            }
        });
    }
};


