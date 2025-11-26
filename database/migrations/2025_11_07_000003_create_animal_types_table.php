<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('animal_types', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->boolean('permite_adopcion')->default(false);
            $table->boolean('permite_liberacion')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('animal_types');
    }
};


