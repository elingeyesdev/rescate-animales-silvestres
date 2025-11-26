<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            if (!Schema::hasColumn('reports', 'condicion_inicial_id')) {
                $table->foreignId('condicion_inicial_id')->nullable()->constrained('animal_conditions');
            }
            if (!Schema::hasColumn('reports', 'tipo_incidente_id')) {
                $table->foreignId('tipo_incidente_id')->nullable()->constrained('incident_types');
            }
            if (!Schema::hasColumn('reports', 'tamano')) {
                $table->string('tamano', 16)->nullable(); // pequeno, mediano, grande
            }
            if (!Schema::hasColumn('reports', 'puede_moverse')) {
                $table->boolean('puede_moverse')->nullable();
            }
            if (!Schema::hasColumn('reports', 'urgencia')) {
                $table->unsignedTinyInteger('urgencia')->nullable(); // 1..5
            }
        });
    }

    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            if (Schema::hasColumn('reports', 'condicion_inicial_id')) {
                $table->dropConstrainedForeignId('condicion_inicial_id');
            }
            if (Schema::hasColumn('reports', 'tipo_incidente_id')) {
                $table->dropConstrainedForeignId('tipo_incidente_id');
            }
            if (Schema::hasColumn('reports', 'tamano')) {
                $table->dropColumn('tamano');
            }
            if (Schema::hasColumn('reports', 'puede_moverse')) {
                $table->dropColumn('puede_moverse');
            }
            if (Schema::hasColumn('reports', 'urgencia')) {
                $table->dropColumn('urgencia');
            }
        });
    }
};





