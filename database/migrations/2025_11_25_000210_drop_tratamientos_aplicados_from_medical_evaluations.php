<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('medical_evaluations', function (Blueprint $table) {
            if (Schema::hasColumn('medical_evaluations', 'tratamientos_aplicados')) {
                $table->dropColumn('tratamientos_aplicados');
            }
        });
    }

    public function down(): void
    {
        Schema::table('medical_evaluations', function (Blueprint $table) {
            if (!Schema::hasColumn('medical_evaluations', 'tratamientos_aplicados')) {
                $table->json('tratamientos_aplicados')->nullable()->after('temperatura');
            }
        });
    }
};




