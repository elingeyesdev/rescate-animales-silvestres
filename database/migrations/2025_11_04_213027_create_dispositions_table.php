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
        Schema::create('dispositions', function (Blueprint $table) {
            $table->id();
            // From Disposicion (diagram)
            $table->enum('tipo', ['traslado','adopcion','liberacion'])->index();
            $table->integer('center_id')->nullable();   //integracion con centro
            $table->float('latitud')->nullable();
            $table->float('longitud')->nullable();
            #$table->foreignId('responsable_id')->constrained('users'); 
            $table->timestamps();                                     
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dispositions');
    }
};
