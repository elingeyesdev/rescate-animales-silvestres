<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Agregar persona_id si no existe aún (inicialmente nullable para permitir la migración de datos)
        if (!Schema::hasColumn('transfers', 'persona_id')) {
            Schema::table('transfers', function (Blueprint $table) {
                $table->foreignId('persona_id')->nullable()->constrained('people')->cascadeOnDelete()->after('id');
            });
        }

        // Migrar datos existentes: persona_id <- rescuers.persona_id a partir de transfers.rescatista_id
        DB::statement("
            UPDATE transfers t
            SET persona_id = r.persona_id
            FROM rescuers r
            WHERE r.id = t.rescatista_id
        ");

        // Hacer persona_id NOT NULL sin requerir doctrine/dbal
        if (Schema::hasColumn('transfers', 'persona_id')) {
            DB::statement('ALTER TABLE ONLY transfers ALTER COLUMN persona_id SET NOT NULL');
        }

        // Eliminar la FK y columna antigua rescatista_id
        // Intentar quitar la FK por nombre si existe (PostgreSQL)
        DB::statement('ALTER TABLE ONLY transfers DROP CONSTRAINT IF EXISTS transfers_rescatista_id_foreign');
        if (Schema::hasColumn('transfers', 'rescatista_id')) {
            Schema::table('transfers', function (Blueprint $table) {
                $table->dropColumn('rescatista_id');
            });
        }
    }

    public function down(): void
    {
        // Restaurar columna rescatista_id y su FK
        if (!Schema::hasColumn('transfers', 'rescatista_id')) {
            Schema::table('transfers', function (Blueprint $table) {
                $table->foreignId('rescatista_id')->nullable()->constrained('rescuers')->cascadeOnDelete()->after('id');
            });
        }

        // Migrar datos inversos: rescatista_id <- rescuers.id a partir de persona_id
        DB::statement("
            UPDATE transfers t
            SET rescatista_id = r.id
            FROM rescuers r
            WHERE r.persona_id = t.persona_id
        ");

        // Hacer no nulo tras migración
        if (Schema::hasColumn('transfers', 'rescatista_id')) {
            DB::statement('ALTER TABLE ONLY transfers ALTER COLUMN rescatista_id SET NOT NULL');
        }

        // Quitar persona_id
        DB::statement('ALTER TABLE ONLY transfers DROP CONSTRAINT IF EXISTS transfers_persona_id_foreign');
        if (Schema::hasColumn('transfers', 'persona_id')) {
            Schema::table('transfers', function (Blueprint $table) {
                $table->dropColumn('persona_id');
            });
        }
    }
};


