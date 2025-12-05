<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incident_types', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 120);
            $table->unsignedTinyInteger('riesgo')->default(1); // 0 bajo, 1 medio, 2 alto
            $table->boolean('activo')->default(true);
        });

        DB::table('incident_types')->insert([
            ['nombre' => 'Incendio', 'riesgo' => 2, 'activo' => true],
            ['nombre' => 'Atropello', 'riesgo' => 2, 'activo' => true],
            ['nombre' => 'Cacería / arma de fuego', 'riesgo' => 2, 'activo' => true],
            ['nombre' => 'Encontrado atrapado', 'riesgo' => 1, 'activo' => true],
            ['nombre' => 'Animal desorientado', 'riesgo' => 1, 'activo' => true],
            ['nombre' => 'Evento natural (inundación, tormenta)', 'riesgo' => 2, 'activo' => true],
            ['nombre' => 'Otro', 'riesgo' => 1, 'activo' => true],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('incident_types');
    }
};





