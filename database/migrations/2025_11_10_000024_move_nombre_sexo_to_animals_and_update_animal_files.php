<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up(): void
	{
		// 1) Add animal_id to animal_files referencing animals
		Schema::table('animal_files', function (Blueprint $table) {
			$table->foreignId('animal_id')->nullable()->constrained('animals')->cascadeOnDelete()->unique();
		});

		// 2) Migrate existing nombre/sexo from animal_files to animals and set animal_id
		DB::table('animal_files')
			->select(['id', 'nombre', 'sexo', 'created_at', 'updated_at'])
			->orderBy('id')
			->chunkById(200, function ($rows) {
				foreach ($rows as $row) {
					$animalId = DB::table('animals')->insertGetId([
						'nombre' => $row->nombre,
						'sexo' => $row->sexo,
						'descripcion' => null,
						'created_at' => $row->created_at,
						'updated_at' => $row->updated_at,
					]);

					DB::table('animal_files')
						->where('id', $row->id)
						->update(['animal_id' => $animalId]);
				}
			});

		// 3) Drop nombre and sexo from animal_files
		Schema::table('animal_files', function (Blueprint $table) {
			$table->dropColumn(['nombre', 'sexo']);
		});

		// Note: leaving animal_id nullable aligns with the provided SQL (no NOT NULL specified)
	}

	public function down(): void
	{
		// 1) Re-add nombre and sexo to animal_files
		Schema::table('animal_files', function (Blueprint $table) {
			$table->string('nombre')->after('id');
			$table->enum('sexo', ['Hembra', 'Macho', 'Desconocido'])->after('nombre');
		});

		// 2) Backfill nombre/sexo from animals when possible
		DB::table('animal_files')
			->select(['id', 'animal_id'])
			->orderBy('id')
			->chunkById(200, function ($rows) {
				foreach ($rows as $row) {
					if ($row->animal_id) {
						$animal = DB::table('animals')->where('id', $row->animal_id)->first();
						if ($animal) {
							DB::table('animal_files')
								->where('id', $row->id)
								->update([
									'nombre' => $animal->nombre ?? '',
									'sexo' => $animal->sexo ?? 'Desconocido',
								]);
						}
					}
				}
			});

		// 3) Drop unique and foreign key on animal_id, then drop the column
		Schema::table('animal_files', function (Blueprint $table) {
			$table->dropUnique(['animal_id']);
		});

		Schema::table('animal_files', function (Blueprint $table) {
			$table->dropConstrainedForeignId('animal_id');
		});
	}
};


