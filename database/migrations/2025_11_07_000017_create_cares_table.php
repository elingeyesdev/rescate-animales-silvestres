<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hoja_animal_id')->constrained('animal_files')->cascadeOnDelete();
            $table->foreignId('tipo_cuidado_id')->constrained('care_types')->cascadeOnDelete();
            $table->text('descripcion')->nullable();
            $table->date('fecha')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cares');
    }
};


