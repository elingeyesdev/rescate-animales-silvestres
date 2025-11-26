<?php

namespace App\Services\Animal;

use App\Models\AnimalFile;
use App\Models\AnimalHistory;
use App\Models\AnimalStatus;
use App\Models\MedicalEvaluation;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AnimalMedicalEvaluationTransactionalService
{
	/**
	 * Crea evaluación médica, actualiza estado en hoja de vida y registra historial.
	 */
	public function registerEvaluation(array $data, ?UploadedFile $image = null): array
	{
		DB::beginTransaction();
		$storedPath = null;
		try {
			$animalFile = AnimalFile::with(['animalStatus','release'])->findOrFail($data['animal_file_id']);
            if ($animalFile->release()->exists()) {
                throw new \DomainException('No se puede registrar evaluación médica: el animal ya fue liberado.');
            }

			// Crear evaluación médica
			$evalData = [
				// Se obtiene por consulta previa al modelo de hoja (no desde el request)
				'animal_file_id' => $animalFile->id,
				'tratamiento_id' => $data['tratamiento_id'],
				'descripcion' => $data['descripcion'] ?? null,
				'diagnostico' => $data['diagnostico'] ?? null,
				'peso' => $data['peso'] ?? null,
				'temperatura' => $data['temperatura'] ?? null,
				'tratamiento_texto' => $data['tratamiento_texto'] ?? null,
				'recomendacion' => $data['recomendacion'] ?? null,
				'apto_traslado' => $data['apto_traslado'] ?? null,
				'fecha' => Carbon::now()->toDateString(),
				'veterinario_id' => $data['veterinario_id'],
			];

			if ($image) {
				$storedPath = $image->store('evidencias/medical-evaluations', 'public');
				$evalData['imagen_url'] = $storedPath;
			}

			$medicalEvaluation = MedicalEvaluation::create($evalData);

			// Actualizar estado del animal en hoja de vida (opcional)
			$oldStatus = null;
			$newStatusModel = null;
			if (!empty($data['estado_id'])) {
				$oldStatus = [
					'id' => $animalFile->estado_id,
					'nombre' => $animalFile->animalStatus?->nombre,
				];
				$newStatusModel = AnimalStatus::findOrFail($data['estado_id']);
				$animalFile->update(['estado_id' => $newStatusModel->id]);
			}

			// Historial
			$historyData = [
				'animal_file_id' => $animalFile->id,
				'valores_antiguos' => null,
				'valores_nuevos' => [
					'evaluacion_medica' => [
						'id' => $medicalEvaluation->id,
						'tratamiento_id' => $medicalEvaluation->tratamiento_id,
						'tratamiento_texto' => $medicalEvaluation->tratamiento_texto,
						'veterinario_id' => $medicalEvaluation->veterinario_id,
						'fecha' => (string) $medicalEvaluation->fecha,
						'diagnostico' => $medicalEvaluation->diagnostico,
						'peso' => $medicalEvaluation->peso,
						'temperatura' => $medicalEvaluation->temperatura,
						'recomendacion' => $medicalEvaluation->recomendacion,
						'apto_traslado' => $medicalEvaluation->apto_traslado,
					],
				],
				'observaciones' => [
					'texto' => $data['observaciones'] ?? 'Evaluación médica',
				],
			];
			if ($oldStatus && $newStatusModel) {
				$historyData['valores_antiguos'] = ['estado' => $oldStatus];
				$historyData['valores_nuevos']['estado'] = [
					'id' => $newStatusModel->id,
					'nombre' => $newStatusModel->nombre,
				];
			}
			AnimalHistory::create($historyData);

			DB::commit();

			return ['evaluation' => $medicalEvaluation, 'animalFile' => $animalFile];
		} catch (\Throwable $e) {
			DB::rollBack();
			if ($storedPath) {
				Storage::disk('public')->delete($storedPath);
			}
			throw $e;
		}
	}
}


