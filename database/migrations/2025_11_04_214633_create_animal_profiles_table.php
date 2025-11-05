<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('animal_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('estado_salud')->nullable();        
            $table->string('sexo')->nullable();                   
            $table->string('especie');                          
            $table->string('raza')->nullable();                 
            $table->string('alimentacion')->nullable();                 
            $table->string('frecuencia')->nullable();             
            $table->integer('cantidad')->nullable();             
            $table->string('color')->nullable();

            //Guardar ruta relativa (Storage::disk('public'))
            $table->string('imagen', 2048)->nullable();  

            $table->unsignedBigInteger('reporte_id')->nullable(); // integracion con reporte
            $table->text('detalle')->nullable();                 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('animal_profiles');
    }
};
