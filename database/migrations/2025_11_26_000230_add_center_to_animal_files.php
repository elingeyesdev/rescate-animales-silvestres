<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('animal_files') && !Schema::hasColumn('animal_files', 'centro_id')) {
            Schema::table('animal_files', function (Blueprint $table) {
                $table->bigInteger('centro_id')->nullable()->after('estado_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('animal_files') && Schema::hasColumn('animal_files', 'centro_id')) {
            Schema::table('animal_files', function (Blueprint $table) {
                $table->dropColumn('centro_id');
            });
        }
    }
};


