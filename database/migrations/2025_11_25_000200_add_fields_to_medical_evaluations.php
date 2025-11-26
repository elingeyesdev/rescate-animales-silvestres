<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('medical_evaluations', function (Blueprint $table) {
            $table->text('diagnostico')->nullable()->after('descripcion');
            $table->decimal('peso', 8, 2)->nullable()->after('diagnostico');
            $table->decimal('temperatura', 5, 2)->nullable()->after('peso');
            $table->json('tratamientos_aplicados')->nullable()->after('tratamiento_id');
            $table->text('tratamiento_texto')->nullable()->after('tratamientos_aplicados');
            $table->string('recomendacion')->nullable()->after('tratamiento_texto'); // traslado, observacion_24h, nueva_revision, tratamiento_prolongado
            $table->string('apto_traslado')->nullable()->after('recomendacion'); // si, no, con_restricciones
        });
    }

    public function down(): void
    {
        Schema::table('medical_evaluations', function (Blueprint $table) {
            $table->dropColumn([
                'diagnostico',
                'peso',
                'temperatura',
                'tratamientos_aplicados',
                'tratamiento_texto',
                'recomendacion',
                'apto_traslado',
            ]);
        });
    }
};




