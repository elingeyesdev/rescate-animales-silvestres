<?php

namespace App\Services\Animal;

use App\Models\AnimalFile;
use App\Models\AnimalHistory;
use App\Models\Release;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AnimalReleaseTransactionalService
{
	private array $allowedStatuses = ['estable','bueno','muy bueno','excelente'];

	public function create(array $data, ?UploadedFile $image = null): Release
	{
        return DB::transaction(function () use ($data, $image) {
            $animalFile = AnimalFile::with('animalStatus','animal')->findOrFail($data['animal_file_id']);

            if ($animalFile->release()->exists()) {
                throw new \DomainException('El animal ya tiene una liberación registrado.');
            }

            $statusName = mb_strtolower((string)($animalFile->animalStatus->nombre ?? ''));
            if (!in_array($statusName, $this->allowedStatuses, true)) {
                throw new \DomainException('El animal no está en un estado de salud apto para liberación.');
            }

            if ($image) {
                $data['imagen_url'] = $image->store('evidencias/releases', 'public');
            }

            // Las liberaciones siempre están aprobadas (solo administradores pueden crearlas)
            $data['aprobada'] = true;

            $release = Release::create($data);

			AnimalHistory::create([
				'animal_file_id' => $animalFile->id,
				'valores_antiguos' => null,
				'valores_nuevos' => [
					'liberacion' => [
						'id' => $release->id,
						'direccion' => $release->direccion,
						'latitud' => $release->latitud,
						'longitud' => $release->longitud,
						'imagen_url' => $release->imagen_url,
					],
				],
				'observaciones' => [
					'texto' => 'Registro de liberación',
				],
                'changed_at' => $release->created_at,
			]);

			return $release;
		});
	}
}




