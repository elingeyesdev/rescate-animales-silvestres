<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('releases', 'animal_file_id')) {
            Schema::table('releases', function (Blueprint $table) {
                $table->foreignId('animal_file_id')->nullable()->constrained('animal_files')->cascadeOnDelete()->after('id');
            });
        }

        DB::statement("
            UPDATE releases r
            SET animal_file_id = af.id
            FROM animal_files af
            WHERE af.liberacion_id = r.id
        ");

        try {
            Schema::table('releases', function (Blueprint $table) {
                $table->unique('animal_file_id');
            });
        } catch (\Throwable $e) {
            // ignorar si ya existe
        }

        // 4) Eliminar FKs y columnas antiguas en animal_files
        DB::statement('ALTER TABLE ONLY animal_files DROP CONSTRAINT IF EXISTS animal_files_liberacion_id_foreign');
        
        if (Schema::hasColumn('animal_files', 'liberacion_id')) {
            Schema::table('animal_files', function (Blueprint $table) {
                $table->dropColumn('liberacion_id');
            });
        }
    }

    public function down(): void
    {
        // 1) Restaurar columnas en animal_files
        
        if (!Schema::hasColumn('animal_files', 'liberacion_id')) {
            Schema::table('animal_files', function (Blueprint $table) {
                $table->foreignId('liberacion_id')->nullable()->constrained('releases')->nullOnDelete()->after('estado_id');
            });
        }

        // 2) Backfill inverso desde releases hacia animal_files
        
        DB::statement("
            UPDATE animal_files af
            SET liberacion_id = r.id
            FROM releases r
            WHERE r.animal_file_id = af.id
        ");

        // 3) Quitar restricciones únicas y FK/columna nuevas en releases
        DB::statement('ALTER TABLE ONLY releases DROP CONSTRAINT IF EXISTS releases_animal_file_id_foreign');

        Schema::table('releases', function (Blueprint $table) {
            try {
                $table->dropUnique(['animal_file_id']);
            } catch (\Throwable $e) {
                // ignorar si no existe
            }
            if (Schema::hasColumn('releases', 'animal_file_id')) {
                $table->dropColumn('animal_file_id');
            }
        });
    }
};


