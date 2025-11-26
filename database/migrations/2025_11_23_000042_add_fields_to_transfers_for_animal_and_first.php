<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transfers', function (Blueprint $table) {
            if (!Schema::hasColumn('transfers', 'primer_traslado')) {
                $table->boolean('primer_traslado')->default(true)->after('observaciones');
            }
            if (!Schema::hasColumn('transfers', 'animal_id')) {
                $table->foreignId('animal_id')->nullable()->after('primer_traslado')->constrained('animals')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('transfers', function (Blueprint $table) {
            if (Schema::hasColumn('transfers', 'animal_id')) {
                $table->dropConstrainedForeignId('animal_id');
            }
            if (Schema::hasColumn('transfers', 'primer_traslado')) {
                $table->dropColumn('primer_traslado');
            }
        });
    }
};







