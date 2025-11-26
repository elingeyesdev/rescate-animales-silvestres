<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('animal_conditions', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 120);
            $table->unsignedTinyInteger('severidad')->default(3); // 1..5
            $table->boolean('activo')->default(true);
        });

        // Seed básico
        DB::table('animal_conditions')->insert([
            ['nombre' => 'Herido leve', 'severidad' => 2, 'activo' => true],
            ['nombre' => 'Herido grave', 'severidad' => 5, 'activo' => true],
            ['nombre' => 'Inconsciente', 'severidad' => 5, 'activo' => true],
            ['nombre' => 'Deshidratado', 'severidad' => 3, 'activo' => true],
            ['nombre' => 'Quemaduras', 'severidad' => 4, 'activo' => true],
            ['nombre' => 'Desorientado / shock', 'severidad' => 3, 'activo' => true],
            ['nombre' => 'Atascado / atrapado', 'severidad' => 4, 'activo' => true],
            ['nombre' => 'Difícil acceso', 'severidad' => 3, 'activo' => true],
            ['nombre' => 'Desconocido', 'severidad' => 3, 'activo' => true],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('animal_conditions');
    }
};





