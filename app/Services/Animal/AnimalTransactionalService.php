<?php

namespace App\Services\Animal;

use App\Models\Animal;
use App\Models\AnimalFile;
use App\Models\AnimalHistory;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AnimalTransactionalService
{
	/**
	 * Crea un Animal y su Hoja de Animal (AnimalFile) en una transacción.
	 *
	 * @param array $animalData Campos para App\Models\Animal
	 * @param array $animalFileData Campos para App\Models\AnimalFile (sin animal_id ni imagen_url)
	 * @param UploadedFile|null $image Archivo de imagen opcional
	 * @return array{animal: Animal, animalFile: AnimalFile}
	 */
	public function createWithFile(array $animalData, array $animalFileData, ?UploadedFile $image = null): array
	{
		DB::beginTransaction();

		$storedPath = null;
		try {
			$animal = Animal::create($animalData);

			if ($image) {
				$storedPath = $image->store('animal_files', 'public');
				$animalFileData['imagen_url'] = $storedPath;
			}

			$animalFileData['animal_id'] = $animal->id;
			$animalFile = AnimalFile::create($animalFileData);

			// Registrar creación de Hoja de Vida en historial
			AnimalHistory::create([
				'animal_file_id' => $animalFile->id,
				'valores_antiguos' => null,
				'valores_nuevos' => [
					'animal' => [
						'id' => $animal->id,
						'nombre' => $animal->nombre,
						'sexo' => $animal->sexo,
					],
					'animal_file' => [
						'id' => $animalFile->id,
						'estado_id' => $animalFile->estado_id ?? null,
						'tipo_id' => $animalFile->tipo_id ?? null,
						'especie_id' => $animalFile->especie_id ?? null,
						'arrived_count' => $animalData['llegaron_cantidad'] ?? null,
					],
				],
				'observaciones' => [
					'texto' => 'Creación de Hoja de Vida',
				],
			]);

			// Reclamar por reporte (auto) si hay report_id
			if (!empty($animalData['reporte_id'])) {
				// Enlazar por report_id en traslados
				AnimalHistory::whereNull('animal_file_id')
					->whereNotNull('valores_nuevos')
					->whereRaw("(valores_nuevos->'transfer'->>'report_id')::text = ?", [(string)$animalData['reporte_id']])
					->update(['animal_file_id' => $animalFile->id]);
				// Enlazar también historiales de tipo 'report' asociados a ese reporte
				AnimalHistory::whereNull('animal_file_id')
					->whereNotNull('valores_nuevos')
					->whereRaw("(valores_nuevos->'report'->>'id')::text = ?", [(string)$animalData['reporte_id']])
					->update(['animal_file_id' => $animalFile->id]);
			}

			DB::commit();

			return [
				'animal' => $animal,
				'animalFile' => $animalFile,
			];
		} catch (\Throwable $e) {
			DB::rollBack();
			if ($storedPath) {
				Storage::disk('public')->delete($storedPath);
			}
			throw $e;
		}
	}
}


