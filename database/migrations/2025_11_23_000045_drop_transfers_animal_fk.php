<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('transfers')) {
            // Intentar eliminar la constraint si existe
            try {
                DB::statement('ALTER TABLE ONLY transfers DROP CONSTRAINT IF EXISTS transfers_animal_id_foreign');
            } catch (\Throwable $e) {
                // ignorar si no existe o ya fue eliminada
            }
            // Asegurar tipo de columna sin FK (bigint nullable)
            Schema::table('transfers', function (Blueprint $table) {
                // En Postgres no se puede cambiar tipo a unsigned; mantenemos bigint
                // Solo aseguramos nullable
                $table->unsignedBigInteger('animal_id')->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('transfers')) {
            Schema::table('transfers', function (Blueprint $table) {
                // restaurar constraint si se desea (opcional)
                // $table->foreign('animal_id')->references('id')->on('animals')->onDelete('cascade');
            });
        }
    }
};


