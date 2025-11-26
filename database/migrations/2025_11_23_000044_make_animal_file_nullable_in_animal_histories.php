<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('animal_histories')) {
            // Postgres: permitir NULL
            DB::statement('ALTER TABLE animal_histories ALTER COLUMN animal_file_id DROP NOT NULL');
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('animal_histories')) {
            // Volver a NOT NULL (puede fallar si existen filas con NULL)
            DB::statement('ALTER TABLE animal_histories ALTER COLUMN animal_file_id SET NOT NULL');
        }
    }
};





