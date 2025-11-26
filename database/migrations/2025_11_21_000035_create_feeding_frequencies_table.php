<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up(): void
	{
		Schema::create('feeding_frequencies', function (Blueprint $table) {
			$table->id();
			$table->string('nombre');
			$table->text('descripcion')->nullable();
			$table->timestamps();
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('feeding_frequencies');
	}
};








