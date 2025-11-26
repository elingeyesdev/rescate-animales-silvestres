<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up(): void
	{
		Schema::create('care_feedings', function (Blueprint $table) {
			$table->id();
			$table->foreignId('care_id')->constrained('cares')->cascadeOnDelete()->nullable();
			$table->foreignId('feeding_type_id')->constrained('feeding_types')->cascadeOnDelete();
			$table->foreignId('feeding_frequency_id')->constrained('feeding_frequencies')->cascadeOnDelete();
			$table->foreignId('feeding_portion_id')->constrained('feeding_portions')->cascadeOnDelete();
			$table->timestamps();
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('care_feedings');
	}
};








