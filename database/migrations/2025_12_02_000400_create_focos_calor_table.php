<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('focos_calor', function (Blueprint $table) {
            $table->id();
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->integer('confidence')->nullable();
            $table->date('acq_date');
            $table->time('acq_time');
            $table->decimal('bright_ti4', 10, 4)->nullable();
            $table->decimal('bright_ti5', 10, 4)->nullable();
            $table->decimal('frp', 10, 4)->nullable();
            $table->timestamps();

            // Índices para búsquedas eficientes
            $table->index(['latitude', 'longitude']);
            $table->index('acq_date');
            $table->index(['acq_date', 'acq_time']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('focos_calor');
    }
};

