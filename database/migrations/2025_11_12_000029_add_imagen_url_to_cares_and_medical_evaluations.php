<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('medical_evaluations', 'imagen_url')) {
            Schema::table('medical_evaluations', function (Blueprint $table) {
                $table->string('imagen_url', 255)->nullable()->after('veterinario_id');
            });
        }

        if (!Schema::hasColumn('cares', 'imagen_url')) {
            Schema::table('cares', function (Blueprint $table) {
                $table->string('imagen_url', 255)->nullable()->after('fecha');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('medical_evaluations', 'imagen_url')) {
            Schema::table('medical_evaluations', function (Blueprint $table) {
                $table->dropColumn('imagen_url');
            });
        }

        if (Schema::hasColumn('cares', 'imagen_url')) {
            Schema::table('cares', function (Blueprint $table) {
                $table->dropColumn('imagen_url');
            });
        }
    }
};


