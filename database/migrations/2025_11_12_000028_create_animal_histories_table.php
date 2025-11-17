<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Renombrar tabla antigua si existe y la nueva no existe
        if (Schema::hasTable('animal_file_history') && !Schema::hasTable('animal_histories')) {
            DB::statement('ALTER TABLE animal_file_history RENAME TO animal_histories');
        }

        if (!Schema::hasTable('animal_histories')) {
            Schema::create('animal_histories', function (Blueprint $table) {
                $table->id();
                $table->foreignId('animal_file_id')->constrained('animal_files')->cascadeOnDelete();
                $table->timestamp('changed_at')->useCurrent();
                $table->json('old_values')->nullable();
                $table->json('new_values')->nullable();
                $table->text('observaciones')->nullable();
                $table->index(['animal_file_id', 'changed_at']);
            });
        } else {
            // Asegurar columnas clave por si viniera de un esquema previo
            Schema::table('animal_histories', function (Blueprint $table) {
                if (!Schema::hasColumn('animal_histories', 'old_values')) {
                    $table->json('old_values')->nullable();
                }
                if (!Schema::hasColumn('animal_histories', 'new_values')) {
                    $table->json('new_values')->nullable();
                }
                if (!Schema::hasColumn('animal_histories', 'observaciones')) {
                    $table->text('observaciones')->nullable();
                }
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('animal_histories');
    }
};


