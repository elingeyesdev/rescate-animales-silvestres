<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Hacer raza_id nullable (PostgreSQL)
        if (Schema::hasTable('animal_files')) {
            DB::statement('ALTER TABLE ONLY animal_files ALTER COLUMN raza_id DROP NOT NULL');
        }
    }

    public function down(): void
    {
        // Revertir a NOT NULL (puede fallar si existen NULLs)
        if (Schema::hasTable('animal_files')) {
            DB::statement('ALTER TABLE ONLY animal_files ALTER COLUMN raza_id SET NOT NULL');
        }
    }
};


