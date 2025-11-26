<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('animal_file_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('animal_file_id')->constrained('animal_files')->cascadeOnDelete();
            $table->timestamp('changed_at')->useCurrent();
            $table->json('estado_anterior');
            $table->json('estado_nuevo');
            $table->json('observaciones');
            $table->index(['animal_file_id', 'changed_at']);
        });

    }

    public function down(): void
    {
        DB::unprepared("
            DROP TRIGGER IF EXISTS trg_animal_files_update ON animal_files;
            DROP FUNCTION IF EXISTS log_animal_files_update();
        ");

        Schema::dropIfExists('animal_file_history');
    }
};



