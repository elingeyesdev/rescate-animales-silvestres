<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Eliminar columnas de estado si existen
        DB::statement('ALTER TABLE ONLY animal_histories DROP COLUMN IF EXISTS estado_anterior');
        DB::statement('ALTER TABLE ONLY animal_histories DROP COLUMN IF EXISTS estado_nuevo');

        // Renombrar columnas JSON a español si existen
        // old_values -> valores_antiguos
        $existsOld = DB::selectOne("
            SELECT 1
            FROM information_schema.columns
            WHERE table_schema = 'public' AND table_name = 'animal_histories' AND column_name = 'old_values'
        ");
        if ($existsOld) {
            DB::statement('ALTER TABLE ONLY animal_histories RENAME COLUMN old_values TO valores_antiguos');
        }

        // new_values -> valores_nuevos
        $existsNew = DB::selectOne("
            SELECT 1
            FROM information_schema.columns
            WHERE table_schema = 'public' AND table_name = 'animal_histories' AND column_name = 'new_values'
        ");
        if ($existsNew) {
            DB::statement('ALTER TABLE ONLY animal_histories RENAME COLUMN new_values TO valores_nuevos');
        }
    }

    public function down(): void
    {
        // Renombrar de vuelta a inglés si existen
        $existsVA = DB::selectOne("
            SELECT 1
            FROM information_schema.columns
            WHERE table_schema = 'public' AND table_name = 'animal_histories' AND column_name = 'valores_antiguos'
        ");
        if ($existsVA) {
            DB::statement('ALTER TABLE ONLY animal_histories RENAME COLUMN valores_antiguos TO old_values');
        }

        $existsVN = DB::selectOne("
            SELECT 1
            FROM information_schema.columns
            WHERE table_schema = 'public' AND table_name = 'animal_histories' AND column_name = 'valores_nuevos'
        ");
        if ($existsVN) {
            DB::statement('ALTER TABLE ONLY animal_histories RENAME COLUMN valores_nuevos TO new_values');
        }

        // Restaurar columnas de estado como json (opcionales)
        DB::statement('ALTER TABLE ONLY animal_histories ADD COLUMN IF NOT EXISTS estado_anterior json NULL');
        DB::statement('ALTER TABLE ONLY animal_histories ADD COLUMN IF NOT EXISTS estado_nuevo json NULL');
    }
};


