<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up(): void
	{
		// 1) Add reporte_id to animals (report has many animals)
		Schema::table('animals', function (Blueprint $table) {
			$table->foreignId('reporte_id')->nullable()->after('descripcion')->constrained('reports')->cascadeOnDelete();
		});

		// 2) Backfill animals.reporte_id from animal_files.reporte_id via the existing link animal_files.animal_id
		DB::table('animal_files')
			->select(['id', 'animal_id', 'reporte_id'])
			->whereNotNull('animal_id')
			->orderBy('id')
			->chunkById(200, function ($rows) {
				foreach ($rows as $row) {
					if ($row->animal_id && $row->reporte_id) {
						DB::table('animals')
							->where('id', $row->animal_id)
							->update(['reporte_id' => $row->reporte_id]);
					}
				}
			});

		// 3) Drop reporte_id from animal_files (animal_files only connects to animals)
		Schema::table('animal_files', function (Blueprint $table) {
			$table->dropConstrainedForeignId('reporte_id');
		});
	}

	public function down(): void
	{
		// 1) Re-add reporte_id to animal_files
		Schema::table('animal_files', function (Blueprint $table) {
			$table->foreignId('reporte_id')->nullable()->constrained('reports')->cascadeOnDelete();
		});

		// 2) Backfill animal_files.reporte_id from animals.reporte_id via animal_files.animal_id
		DB::table('animal_files')
			->select(['animal_files.id', 'animal_files.animal_id'])
			->whereNotNull('animal_files.animal_id')
			->orderBy('animal_files.id')
			->chunkById(200, function ($rows) {
				foreach ($rows as $row) {
					$reporte = DB::table('animals')->where('id', $row->animal_id)->value('reporte_id');
					if ($reporte) {
						DB::table('animal_files')
							->where('id', $row->id)
							->update(['reporte_id' => $reporte]);
					}
				}
			});

		// 3) Drop reporte_id from animals
		Schema::table('animals', function (Blueprint $table) {
			$table->dropConstrainedForeignId('reporte_id');
		});
	}
};


