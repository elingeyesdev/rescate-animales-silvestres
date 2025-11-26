<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rescuers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('persona_id')->constrained('people')->cascadeOnDelete();
            $table->boolean('cv_documentado')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rescuers');
    }
};


