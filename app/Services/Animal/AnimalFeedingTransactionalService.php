<?php

namespace App\Services\Animal;

use App\Models\AnimalFile;
use App\Models\AnimalHistory;
use App\Models\Care;
use App\Models\CareFeeding;
use App\Models\CareType;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AnimalFeedingTransactionalService
{
	/**
	 * Registra Alimentación: crea Care (tipo Alimentación) + CareFeeding + AnimalHistory.
	 * Maneja commit/rollback explícitos.
	 *
	 * @param array $data Datos validados desde FeedingProcessRequest
	 * @return array{care: Care, careFeeding: CareFeeding}
	 */
	public function registerFeeding(array $data): array
	{
		DB::beginTransaction();
		try {
			$animalFile = AnimalFile::findOrFail($data['animal_file_id']);

			$careTypeId = CareType::where('nombre', 'LIKE', '%Aliment%')->value('id');
			if (!$careTypeId) {
				$careType = CareType::create([
					'nombre' => 'Alimentación',
					'descripcion' => 'Cuidados relacionados con alimentación',
				]);
				$careTypeId = $careType->id;
			}

			$care = Care::create([
				'hoja_animal_id' => $animalFile->id,
				'tipo_cuidado_id' => $careTypeId,
				'descripcion' => $data['descripcion'] ?? ('Registro de alimentación del animal #' . ($animalFile->animal?->id ?? $animalFile->id)),
				'fecha' => Carbon::now(),
			]);

			$careFeeding = CareFeeding::create([
				'care_id' => $care->id,
				'feeding_type_id' => $data['feeding_type_id'],
				'feeding_frequency_id' => $data['feeding_frequency_id'],
				'feeding_portion_id' => $data['feeding_portion_id'],
			]);

			AnimalHistory::create([
				'animal_file_id' => $animalFile->id,
				'valores_antiguos' => null,
				'valores_nuevos' => [
					'care' => [
						'id' => $care->id,
						'descripcion' => $care->descripcion,
						'fecha' => (string) $care->fecha,
						'tipo_cuidado_id' => $care->tipo_cuidado_id,
					],
					'care_feeding' => [
						'id' => $careFeeding->id,
						'feeding_type_id' => $careFeeding->feeding_type_id,
						'feeding_frequency_id' => $careFeeding->feeding_frequency_id,
						'feeding_portion_id' => $careFeeding->feeding_portion_id,
					],
				],
				'observaciones' => [
					'texto' => $data['observaciones'] ?? 'Registro de alimentación',
				],
			]);

			DB::commit();

			return ['care' => $care, 'careFeeding' => $careFeeding];
		} catch (\Throwable $e) {
			DB::rollBack();
			throw $e;
		}
	}
}


