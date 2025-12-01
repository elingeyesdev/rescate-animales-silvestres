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
        Schema::table('releases', function (Blueprint $table) {
            if (!Schema::hasColumn('releases', 'imagen_url')) {
                $table->string('imagen_url')->nullable()->after('detalle');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('releases', function (Blueprint $table) {
            if (Schema::hasColumn('releases', 'imagen_url')) {
                $table->dropColumn('imagen_url');
            }
        });
    }
};
