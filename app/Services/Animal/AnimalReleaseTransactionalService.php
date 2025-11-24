<?php

namespace App\Services\Animal;

use App\Models\AnimalFile;
use App\Models\AnimalHistory;
use App\Models\Release;
use Illuminate\Support\Facades\DB;

class AnimalReleaseTransactionalService
{
	private array $allowedStatuses = ['estable','bueno','muy bueno','excelente'];

	public function create(array $data): Release
	{
        return DB::transaction(function () use ($data) {
            $animalFile = AnimalFile::with('animalStatus','animal','animalType')->findOrFail($data['animal_file_id']);

            if ($animalFile->adoption()->exists()) {
                throw new \DomainException('El animal ya fue adoptado; no puede ser liberado.');
            }
            if ($animalFile->release()->exists()) {
                throw new \DomainException('El animal ya tiene una liberación registrado.');
            }

            if (!$animalFile->animalType?->permite_liberacion) {
                throw new \DomainException('Este tipo de animal no permite liberación.');
            }

            $statusName = mb_strtolower((string)($animalFile->animalStatus->nombre ?? ''));
            if (!in_array($statusName, $this->allowedStatuses, true)) {
                throw new \DomainException('El animal no está en un estado de salud apto para liberación.');
            }

            $release = Release::create($data);

			AnimalHistory::create([
				'animal_file_id' => $animalFile->id,
				'valores_antiguos' => null,
				'valores_nuevos' => [
					'liberacion' => [
						'id' => $release->id,
						'aprobada' => (bool)$release->aprobada,
						'direccion' => $release->direccion,
						'latitud' => $release->latitud,
						'longitud' => $release->longitud,
					],
				],
				'observaciones' => [
					'texto' => 'Registro de liberación',
				],
			]);

			return $release;
		});
	}
}




