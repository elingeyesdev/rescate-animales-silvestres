<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up(): void
	{
		Schema::table('care_types', function (Blueprint $table) {
			$table->boolean('es_alimentacion')->default(false)->after('descripcion');
		});
	}

	public function down(): void
	{
		Schema::table('care_types', function (Blueprint $table) {
			$table->dropColumn('es_alimentacion');
		});
	}
};








