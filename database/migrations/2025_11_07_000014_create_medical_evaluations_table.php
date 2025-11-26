<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medical_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tratamiento_id')->constrained('treatment_types')->cascadeOnDelete();
            $table->text('descripcion')->nullable();
            $table->date('fecha')->nullable();
            $table->foreignId('veterinario_id')->constrained('veterinarians')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medical_evaluations');
    }
};


