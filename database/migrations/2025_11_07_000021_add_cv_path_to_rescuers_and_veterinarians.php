<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rescuers', function (Blueprint $table) {
            $table->dropColumn('cv_documentado');
            $table->string('cv_documentado')->nullable();
        });
        Schema::table('veterinarians', function (Blueprint $table) {
            $table->dropColumn('cv_documentado');
            $table->string('cv_documentado')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('rescuers', function (Blueprint $table) {
            $table->dropColumn('cv_documentado');
            $table->boolean('cv_documentado')->default(false);
        });
        Schema::table('veterinarians', function (Blueprint $table) {
            $table->dropColumn('cv_documentado');
            $table->boolean('cv_documentado')->default(false);
        });
    }
};


