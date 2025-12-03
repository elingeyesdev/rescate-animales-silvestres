<?php

namespace App\Services\Animal;

use App\Models\AnimalFile;
use App\Models\AnimalHistory;
use App\Models\Care;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Services\User\UserTrackingService;

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
			$animalFile = AnimalFile::with('release')->findOrFail($data['animal_file_id']);
            if ($animalFile->release()->exists()) {
                throw new \DomainException('No se puede registrar cuidado: el animal ya fue liberado.');
            }

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

			// Registrar tracking de cuidado
			try {
				app(UserTrackingService::class)->logCare($care, $animalFile);
			} catch (\Exception $e) {
				\Log::warning('Error registrando tracking de cuidado: ' . $e->getMessage());
			}

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




