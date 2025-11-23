<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('medical_evaluations', function (Blueprint $table) {
            if (!Schema::hasColumn('medical_evaluations', 'animal_file_id')) {
                $table->unsignedBigInteger('animal_file_id')->nullable()->after('veterinario_id');
            }
            if (!Schema::hasColumn('medical_evaluations', 'imagen_url')) {
                $table->string('imagen_url')->nullable()->after('animal_file_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('medical_evaluations', function (Blueprint $table) {
            if (Schema::hasColumn('medical_evaluations', 'imagen_url')) {
                $table->dropColumn('imagen_url');
            }
            if (Schema::hasColumn('medical_evaluations', 'animal_file_id')) {
                $table->dropColumn('animal_file_id');
            }
        });
    }
};


