<?php

namespace App\Services\Animal;

use App\Models\Animal;
use App\Models\AnimalFile;
use App\Models\AnimalHistory;
use App\Models\Report;
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
		$copiedFromReport = null;
		try {
			$animal = Animal::create($animalData);

			if ($image) {
				$storedPath = $image->store('animal_files', 'public');
				$animalFileData['imagen_url'] = $storedPath;
			} elseif (!empty($animalData['reporte_id'])) {
				// Si no se sube imagen, intentar copiar la del reporte
				$rep = Report::find($animalData['reporte_id']);
				if ($rep && $rep->imagen_url) {
					$basename = basename($rep->imagen_url);
					$target = 'animal_files/' . uniqid('from_report_') . '_' . $basename;
					if (Storage::disk('public')->exists($rep->imagen_url)) {
						if (Storage::disk('public')->copy($rep->imagen_url, $target)) {
							$animalFileData['imagen_url'] = $target;
							$copiedFromReport = $target;
						}
					}
				}
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
						// estado actual (seleccionado)
						'estado_id' => $animalFile->estado_id ?? null,
						'tipo_id' => $animalFile->tipo_id ?? null,
						'especie_id' => $animalFile->especie_id ?? null,
						'arrived_count' => $animalData['llegaron_cantidad'] ?? null,
						// estado inicial (desde condición del reporte si viene)
						'estado_inicial_id' => $animalData['estado_inicial_id'] ?? null,
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
			if ($copiedFromReport) {
				Storage::disk('public')->delete($copiedFromReport);
			}
			throw $e;
		}
	}

	/**
	 * Crea múltiples Animales y sus Hojas desde un mismo conjunto de datos.
	 *
	 * @param int $count
	 * @param array $animalData
	 * @param array $animalFileData
	 * @param UploadedFile|null $image
	 * @return array<int, array{animal: Animal, animalFile: AnimalFile}>
	 */
	public function createManyWithFile(int $count, array $animalData, array $animalFileData, ?UploadedFile $image = null): array
	{
		if ($count < 1) $count = 1;

		DB::beginTransaction();
		$results = [];
		$firstStored = null;
		$firstCreatedPaths = [];
		try {
			for ($i = 0; $i < $count; $i++) {
				$storedPath = null;
				$animalDataEach = $animalData;
				// Para modo por cada, el arrived_count = 1 en el historial
				$animalDataEach['llegaron_cantidad'] = 1;

				$animal = Animal::create($animalDataEach);

				$afData = $animalFileData;
				if ($image) {
					if ($i === 0) {
						$firstStored = $image->store('animal_files', 'public');
						$afData['imagen_url'] = $firstStored;
					} else {
						$copyTo = 'animal_files/' . uniqid('copy_') . '_' . basename($firstStored);
						Storage::disk('public')->copy($firstStored, $copyTo);
						$afData['imagen_url'] = $copyTo;
						$firstCreatedPaths[] = $copyTo;
					}
				} elseif (!empty($animalData['reporte_id'])) {
					$rep = Report::find($animalData['reporte_id']);
					if ($rep && $rep->imagen_url && Storage::disk('public')->exists($rep->imagen_url)) {
						$copyTo = 'animal_files/' . uniqid('from_report_') . '_' . basename($rep->imagen_url);
						Storage::disk('public')->copy($rep->imagen_url, $copyTo);
						$afData['imagen_url'] = $copyTo;
						$firstCreatedPaths[] = $copyTo;
					}
				}

				$afData['animal_id'] = $animal->id;
				$animalFile = AnimalFile::create($afData);

				// Historial (arrived_count = 1)
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
							'arrived_count' => 1,
						],
					],
					'observaciones' => [
						'texto' => 'Creación de Hoja de Vida',
					],
				]);

				// Enlazar historiales por reporte si aplica
				if (!empty($animalData['reporte_id'])) {
					AnimalHistory::whereNull('animal_file_id')
						->whereNotNull('valores_nuevos')
						->whereRaw("(valores_nuevos->'transfer'->>'report_id')::text = ?", [(string)$animalData['reporte_id']])
						->update(['animal_file_id' => $animalFile->id]);
					AnimalHistory::whereNull('animal_file_id')
						->whereNotNull('valores_nuevos')
						->whereRaw("(valores_nuevos->'report'->>'id')::text = ?", [(string)$animalData['reporte_id']])
						->update(['animal_file_id' => $animalFile->id]);
				}

				$results[] = ['animal' => $animal, 'animalFile' => $animalFile];
			}

			DB::commit();
			return $results;
		} catch (\Throwable $e) {
			DB::rollBack();
			if ($firstStored) {
				Storage::disk('public')->delete($firstStored);
			}
			foreach ($firstCreatedPaths as $p) {
				Storage::disk('public')->delete($p);
			}
			throw $e;
		}
	}
}


