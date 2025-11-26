<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Postgres: quitar NOT NULL
        if (Schema::hasTable('medical_evaluations')) {
            DB::statement('ALTER TABLE medical_evaluations ALTER COLUMN tratamiento_id DROP NOT NULL');
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('medical_evaluations')) {
            DB::statement('ALTER TABLE medical_evaluations ALTER COLUMN tratamiento_id SET NOT NULL');
        }
    }
};







