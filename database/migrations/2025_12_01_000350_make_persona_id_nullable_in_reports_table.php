<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Primero, eliminar la restricción de foreign key si existe
        Schema::table('reports', function (Blueprint $table) {
            // Eliminar la foreign key constraint
            $table->dropForeign(['persona_id']);
        });

        // Luego, modificar la columna para hacerla nullable
        DB::statement('ALTER TABLE ONLY reports ALTER COLUMN persona_id DROP NOT NULL');

        // Finalmente, volver a agregar la foreign key constraint pero permitiendo NULL
        Schema::table('reports', function (Blueprint $table) {
            $table->foreign('persona_id')
                ->references('id')
                ->on('people')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        // Primero, eliminar la foreign key constraint
        Schema::table('reports', function (Blueprint $table) {
            $table->dropForeign(['persona_id']);
        });

        // Eliminar registros con persona_id NULL antes de hacer la columna NOT NULL
        DB::statement('DELETE FROM reports WHERE persona_id IS NULL');

        // Hacer la columna NOT NULL nuevamente
        DB::statement('ALTER TABLE ONLY reports ALTER COLUMN persona_id SET NOT NULL');

        // Volver a agregar la foreign key constraint
        Schema::table('reports', function (Blueprint $table) {
            $table->foreign('persona_id')
                ->references('id')
                ->on('people')
                ->onDelete('cascade');
        });
    }
};

