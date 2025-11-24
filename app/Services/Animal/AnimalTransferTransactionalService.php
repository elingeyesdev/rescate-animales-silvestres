<?php

namespace App\Services\Animal;

use App\Models\AnimalFile;
use App\Models\AnimalHistory;
use App\Models\Transfer;
use Illuminate\Support\Facades\DB;

class AnimalTransferTransactionalService
{
	public function create(array $data): Transfer
	{
		return DB::transaction(function () use ($data) {
            $transfer = Transfer::create($data);

			// Si no es primer traslado y se conoce el animal -> asignar a su hoja
            if (!empty($data['animal_id'])) {
				$animalFile = AnimalFile::where('animal_id', $data['animal_id'])
					->orderByDesc('id')
					->first();
				if ($animalFile) {
					AnimalHistory::create([
						'animal_file_id' => $animalFile->id,
						'valores_antiguos' => null,
						'valores_nuevos' => [
							'transfer' => [
								'id' => $transfer->id,
								'persona_id' => $transfer->persona_id,
								'centro_id' => $transfer->centro_id,
								'observaciones' => $transfer->observaciones,
                                'primer_traslado' => false,
								'latitud' => $transfer->latitud ?? null,
								'longitud' => $transfer->longitud ?? null,
							],
						],
						'observaciones' => [
							'texto' => 'Registro de traslado',
						],
					]);
				}
			} else {
				// Primer traslado desde reporte de hallazgo (sin animal aún)
				AnimalHistory::create([
					'animal_file_id' => null,
					'valores_antiguos' => null,
					'valores_nuevos' => [
						'transfer' => [
							'id' => $transfer->id,
							'persona_id' => $transfer->persona_id,
							'centro_id' => $transfer->centro_id,
							'observaciones' => $transfer->observaciones,
							'primer_traslado' => true,
							'latitud' => $transfer->latitud ?? null,
							'longitud' => $transfer->longitud ?? null,
							'report_id' => $data['reporte_id'] ?? null,
						],
					],
					'observaciones' => [
						'texto' => 'Primer traslado desde reporte de hallazgo',
					],
				]);
			}

			return $transfer;
		});
	}
}




