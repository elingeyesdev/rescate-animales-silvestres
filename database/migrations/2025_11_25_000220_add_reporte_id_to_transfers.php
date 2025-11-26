<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('transfers') && !Schema::hasColumn('transfers', 'reporte_id')) {
            Schema::table('transfers', function (Blueprint $table) {
                // Campo simple sin FK directa; se rellenará por lógica de aplicación/queries
                $table->bigInteger('reporte_id')->nullable()->after('persona_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('transfers') && Schema::hasColumn('transfers', 'reporte_id')) {
            Schema::table('transfers', function (Blueprint $table) {
                $table->dropColumn('reporte_id');
            });
        }
    }
};


