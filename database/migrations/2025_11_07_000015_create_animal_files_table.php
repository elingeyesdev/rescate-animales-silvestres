<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('animal_files', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->enum('sexo', ['Hembra', 'Macho', 'Desconocido']);
            $table->foreignId('reporte_id')->constrained('reports')->cascadeOnDelete();
            $table->foreignId('especie_id')->constrained('species')->cascadeOnDelete();
            $table->string('imagen_url')->nullable();
            $table->foreignId('estado_id')->constrained('animal_statuses')->cascadeOnDelete();
            $table->foreignId('liberacion_id')->nullable()->constrained('releases')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('animal_files');
    }
};


