<?php

namespace App\Services\Animal;

use App\Models\AnimalFile;
use App\Models\AnimalHistory;
use App\Models\Care;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AnimalCareTransactionalService
{
	/**
	 * Registra un Cuidado para una Hoja de Animal y crea un historial asociado.
	 */
	public function registerCare(array $data, ?UploadedFile $image = null): array
	{
		DB::beginTransaction();
		$storedPath = null;
		try {
			$animalFile = AnimalFile::findOrFail($data['animal_file_id']);

            $careData = [
				'hoja_animal_id' => $animalFile->id,
				'tipo_cuidado_id' => $data['tipo_cuidado_id'],
				'descripcion' => $data['descripcion'] ?? null,
                'fecha' => Carbon::now(),
			];

			if ($image) {
				$storedPath = $image->store('evidencias/cares', 'public');
				$careData['imagen_url'] = $storedPath;
			}

			$care = Care::create($careData);

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
				],
                'observaciones' => null,
			]);

			DB::commit();

			return ['care' => $care, 'animalFile' => $animalFile];
		} catch (\Throwable $e) {
			DB::rollBack();
			if ($storedPath) {
				Storage::disk('public')->delete($storedPath);
			}
			throw $e;
		}
	}
}




