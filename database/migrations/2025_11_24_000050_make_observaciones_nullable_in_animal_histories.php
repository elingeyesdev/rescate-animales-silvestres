<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('animal_histories')) {
            // En Postgres, alterar la nulabilidad del campo JSON requiere SQL directo
            DB::statement('ALTER TABLE ONLY animal_histories ALTER COLUMN observaciones DROP NOT NULL');
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('animal_histories')) {
            // Volver a NOT NULL (si fuese necesario)
            DB::statement('ALTER TABLE ONLY animal_histories ALTER COLUMN observaciones SET NOT NULL');
        }
    }
};


