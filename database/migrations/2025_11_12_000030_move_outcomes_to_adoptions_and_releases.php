<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1) Agregar animal_file_id a adoptions y releases (nullable para backfill)
        if (!Schema::hasColumn('adoptions', 'animal_file_id')) {
            Schema::table('adoptions', function (Blueprint $table) {
                $table->foreignId('animal_file_id')->nullable()->constrained('animal_files')->cascadeOnDelete()->after('id');
            });
        }
        if (!Schema::hasColumn('releases', 'animal_file_id')) {
            Schema::table('releases', function (Blueprint $table) {
                $table->foreignId('animal_file_id')->nullable()->constrained('animal_files')->cascadeOnDelete()->after('id');
            });
        }

        // 2) Backfill desde animal_files.adopcion_id / .liberacion_id
        DB::statement("
            UPDATE adoptions a
            SET animal_file_id = af.id
            FROM animal_files af
            WHERE af.adopcion_id = a.id
        ");
        DB::statement("
            UPDATE releases r
            SET animal_file_id = af.id
            FROM animal_files af
            WHERE af.liberacion_id = r.id
        ");

        // 3) Hacer únicos por animal_file_id (permitiendo NULL)
        // Postgres permite múltiples NULL en índices únicos
        try {
            Schema::table('adoptions', function (Blueprint $table) {
                $table->unique('animal_file_id');
            });
        } catch (\Throwable $e) {
            // ignorar si ya existe
        }
        try {
            Schema::table('releases', function (Blueprint $table) {
                $table->unique('animal_file_id');
            });
        } catch (\Throwable $e) {
            // ignorar si ya existe
        }

        // 4) Eliminar FKs y columnas antiguas en animal_files
        DB::statement('ALTER TABLE ONLY animal_files DROP CONSTRAINT IF EXISTS animal_files_adopcion_id_foreign');
        DB::statement('ALTER TABLE ONLY animal_files DROP CONSTRAINT IF EXISTS animal_files_liberacion_id_foreign');
        if (Schema::hasColumn('animal_files', 'adopcion_id')) {
            Schema::table('animal_files', function (Blueprint $table) {
                $table->dropColumn('adopcion_id');
            });
        }
        if (Schema::hasColumn('animal_files', 'liberacion_id')) {
            Schema::table('animal_files', function (Blueprint $table) {
                $table->dropColumn('liberacion_id');
            });
        }
    }

    public function down(): void
    {
        // 1) Restaurar columnas en animal_files
        if (!Schema::hasColumn('animal_files', 'adopcion_id')) {
            Schema::table('animal_files', function (Blueprint $table) {
                $table->foreignId('adopcion_id')->nullable()->constrained('adoptions')->nullOnDelete()->after('estado_id');
            });
        }
        if (!Schema::hasColumn('animal_files', 'liberacion_id')) {
            Schema::table('animal_files', function (Blueprint $table) {
                $table->foreignId('liberacion_id')->nullable()->constrained('releases')->nullOnDelete()->after('adopcion_id');
            });
        }

        // 2) Backfill inverso desde adoptions/releases hacia animal_files
        DB::statement("
            UPDATE animal_files af
            SET adopcion_id = a.id
            FROM adoptions a
            WHERE a.animal_file_id = af.id
        ");
        DB::statement("
            UPDATE animal_files af
            SET liberacion_id = r.id
            FROM releases r
            WHERE r.animal_file_id = af.id
        ");

        // 3) Quitar restricciones únicas y FK/columna nuevas en adoptions/releases
        DB::statement('ALTER TABLE ONLY adoptions DROP CONSTRAINT IF EXISTS adoptions_animal_file_id_foreign');
        DB::statement('ALTER TABLE ONLY releases DROP CONSTRAINT IF EXISTS releases_animal_file_id_foreign');

        Schema::table('adoptions', function (Blueprint $table) {
            try {
                $table->dropUnique(['animal_file_id']);
            } catch (\Throwable $e) {
                // ignorar si no existe
            }
            if (Schema::hasColumn('adoptions', 'animal_file_id')) {
                $table->dropColumn('animal_file_id');
            }
        });
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


