<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_tracking', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete()->comment('Usuario sobre el que se registra la acción');
            $table->unsignedBigInteger('performed_by')->nullable()->comment('ID del usuario que realizó la acción (se llena mediante consulta, sin foreign key)');
            $table->string('action_type', 100)->comment('Tipo de acción en español: registro, aprobacion, rechazo, evaluacion_medica, traslado, cuidado, alimentacion, liberacion, reporte_creado, etc.');
            $table->string('action_description', 255)->comment('Descripción legible de la acción en español');
            $table->string('related_model_type', 100)->nullable()->comment('Tipo de modelo relacionado: Veterinarian, Rescuer, MedicalEvaluation, Report, Transfer, Care, Release, etc.');
            $table->unsignedBigInteger('related_model_id')->nullable()->comment('ID del modelo relacionado');
            $table->json('valores_antiguos')->nullable()->comment('Valores anteriores (si aplica)');
            $table->json('valores_nuevos')->nullable()->comment('Valores nuevos (si aplica)');
            $table->json('metadata')->nullable()->comment('Información adicional: qué animal evaluó, qué reporte aprobó, centro de destino, etc.');
            $table->timestamp('realizado_en')->useCurrent()->comment('Fecha y hora en que se realizó la acción');
            $table->timestamps();

            // Índices para búsquedas eficientes
            $table->index(['user_id', 'realizado_en']);
            $table->index(['performed_by', 'realizado_en']);
            $table->index('action_type');
            $table->index(['related_model_type', 'related_model_id']);
            $table->index('realizado_en');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_tracking');
    }
};

