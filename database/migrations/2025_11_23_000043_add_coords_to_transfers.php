<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transfers', function (Blueprint $table) {
            if (!Schema::hasColumn('transfers', 'latitud')) {
                $table->decimal('latitud', 10, 7)->nullable()->after('animal_id');
            }
            if (!Schema::hasColumn('transfers', 'longitud')) {
                $table->decimal('longitud', 10, 7)->nullable()->after('latitud');
            }
        });
    }

    public function down(): void
    {
        Schema::table('transfers', function (Blueprint $table) {
            if (Schema::hasColumn('transfers', 'longitud')) {
                $table->dropColumn('longitud');
            }
            if (Schema::hasColumn('transfers', 'latitud')) {
                $table->dropColumn('latitud');
            }
        });
    }
};





