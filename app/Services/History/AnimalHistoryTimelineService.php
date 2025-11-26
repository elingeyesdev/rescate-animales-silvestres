<?php

namespace App\Services\History;

use App\Models\AnimalHistory;
use App\Models\AnimalStatus;
use App\Models\CareType;
use App\Models\FeedingFrequency;
use App\Models\FeedingPortion;
use App\Models\FeedingType;
use App\Models\TreatmentType;
use App\Models\Veterinarian;
use App\Models\Care;
use App\Models\MedicalEvaluation;
use App\Models\Person;
use App\Models\Center;
use App\Models\AnimalType;
use App\Models\Species;
use App\Models\Report;
use App\Models\AnimalFile as AnimalFileModel;
use Illuminate\Support\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class AnimalHistoryTimelineService
{
	/**
	 * Retorna la última entrada de historial por hoja de animal, paginada.
	 */
	public function latestPerAnimalFile(int $perPage = 20): LengthAwarePaginator
	{
        // Excluir historiales sin hoja de animal
		$latestIds = AnimalHistory::whereNotNull('animal_file_id')
            ->select(DB::raw('MAX(id) as id'))
			->groupBy('animal_file_id')
			->pluck('id');

		return AnimalHistory::with(['animalFile.animal'])
			->whereIn('id', $latestIds)
			->orderByDesc('id')
			->paginate($perPage);
	}

	public function latestPerAnimalFileOrdered(string $order = 'desc', int $perPage = 20): LengthAwarePaginator
	{
        // Excluir historiales sin hoja de animal
		$latestIds = AnimalHistory::whereNotNull('animal_file_id')
            ->select(DB::raw('MAX(id) as id'))
			->groupBy('animal_file_id')
			->pluck('id');

		$q = AnimalHistory::with(['animalFile.animal'])
			->whereIn('id', $latestIds);
		$order = strtolower($order) === 'asc' ? 'asc' : 'desc';
		$q = $order === 'asc' ? $q->orderBy('id') : $q->orderByDesc('id');
		return $q->paginate($perPage);
	}

	/**
	 * Construye el timeline completo para una hoja de animal.
	 *
	 * @return array<int, array<string, mixed>>
	 */
	public function buildForAnimalFile(int $animalFileId): array
	{
		$all = AnimalHistory::where('animal_file_id', $animalFileId)
			->orderByDesc('changed_at')
			->orderByDesc('id')
			->get();

		$afRecord = AnimalFileModel::find($animalFileId);
		$arrivalImage = $afRecord?->animal?->report?->imagen_url; // no usar como fallback global

		// Cargar catálogos para resolución de nombres
		$statuses = AnimalStatus::all()->keyBy('id');
		$treatments = TreatmentType::all()->keyBy('id');
		$feedTypes = FeedingType::all()->keyBy('id');
		$feedFreqs = FeedingFrequency::all()->keyBy('id');
		$feedPortions = FeedingPortion::all()->keyBy('id');
		$vets = Veterinarian::with('person')->get()->keyBy('id');

		$timeline = [];
		foreach ($all as $h) {
			$changed = $h->changed_at ? Carbon::parse($h->changed_at) : null;
			$changedDate = $changed ? $changed->format('d/m/y') : null;
			$changedTime = $changed ? $changed->format('H:i') : null;
			$item = [
				'id' => $h->id,
				'changed_at' => trim(($changedDate ?? '') . ($changedTime ? ' '.$changedTime : '')),
				'changed_at_label' => ($changedDate || $changedTime)
					? trim('Fecha: '.($changedDate ?? '-') . '    ' . 'Hora: '.($changedTime ?? '-'))
					: null,
				'title' => 'Actualización',
				'details' => [],
			];

			$old = $h->valores_antiguos ?? [];
			$new = $h->valores_nuevos ?? [];
			$imageUrl = null;

			// Reporte (hallazgo)
			if (!empty($new['report'])) {
				$item['title'] = 'Reporte de hallazgo';
				$rp = $new['report'];
				$personName = isset($rp['persona_id']) ? (Person::find($rp['persona_id'])->nombre) : null;
				// imagen desde reporte si existe
				if (!empty($rp['id'])) {
					$reportModel = Report::find($rp['id']);
					if ($reportModel && $reportModel->imagen_url) {
						$imageUrl = $reportModel->imagen_url;
					}
				}
                // Resolver catálogos si llegan por JSON
                $condText = null;
                if (!empty($rp['condicion_inicial_id'])) {
                    $condModel = \App\Models\AnimalCondition::find($rp['condicion_inicial_id']);
                    $condText = $condModel?->nombre;
                }
                $incText = null;
                if (!empty($rp['tipo_incidente_id'])) {
                    $incModel = \App\Models\IncidentType::find($rp['tipo_incidente_id']);
                    $incText = $incModel?->nombre;
                }
				$item['details'][] = [
					'label' => 'Detalle',
					'value' => implode(' | ', array_filter([
						$personName ? ('Reportado por: '.$personName) : null,
						!empty($rp['direccion']) ? ('Dirección: '.$rp['direccion']) : null,
					])),
				];
                if ($condText) {
                    $item['details'][] = ['label' => 'Condición', 'value' => $condText];
                }
                if ($incText) {
                    $item['details'][] = ['label' => 'Incidente', 'value' => $incText];
                }
                if (array_key_exists('tamano', $rp)) {
                    $mapSize = ['pequeno' => 'Pequeño', 'mediano' => 'Mediano', 'grande' => 'Grande'];
                    $item['details'][] = ['label' => 'Tamaño', 'value' => $mapSize[$rp['tamano']] ?? (string)$rp['tamano']];
                }
                if (array_key_exists('puede_moverse', $rp)) {
                    $item['details'][] = ['label' => '¿Puede moverse?', 'value' => ($rp['puede_moverse'] ? 'Sí' : 'No')];
                }
                if (!empty($rp['urgencia'])) {
                    $item['details'][] = ['label' => 'Urgencia', 'value' => (string)$rp['urgencia'].'/5'];
                }
			}

			// Cambio de estado
			if (!empty($new['estado'])) {
				$oldName = isset($old['estado']['id']) ? ($statuses[$old['estado']['id']]->nombre) : ($old['estado']['nombre'] ?? null);
				$newName = isset($new['estado']['id']) ? ($statuses[$new['estado']['id']]->nombre) : ($new['estado']['nombre'] ?? null);

				// Si hay cambio real, mostrar "Anterior → Actual".
				if ($oldName && $newName && $oldName !== $newName) {
					$item['title'] = 'Cambio de estado';
					$item['details'][] = [
						'label' => 'Detalle',
						'value' => $oldName.' → '.$newName,
					];
				} else {
					// Si no hay cambio (o no se conoce el anterior), solo mostrar el estado actual.
					$item['title'] = 'Estado';
					$item['details'][] = [
						'label' => 'Estado actual',
						'value' => $newName ?? $oldName ?? '',
					];
				}
			}

			// Evaluación médica
			if (!empty($new['evaluacion_medica'])) {
				$item['title'] = 'Evaluación Médica';
				$em = $new['evaluacion_medica'];
				$trat = isset($em['tratamiento_id']) ? ($treatments[$em['tratamiento_id']]->nombre) : null;
				$vet = isset($em['veterinario_id']) ? ($vets[$em['veterinario_id']]->person->nombre) : null;
				// intentar obtener imagen desde la evaluación
				if (!empty($em['id'])) {
					$me = MedicalEvaluation::find($em['id']);
					if ($me && $me->imagen_url) {
						$imageUrl = $me->imagen_url;
					}
				}
				$item['details'][] = [
					'label' => 'Información',
					'value' => implode(' | ', array_filter([
						$trat ? ('Tratamiento: '.$trat) : null,
						$vet ? ('Veterinario: '.$vet) : null,
					])),
				];
			}

			// Cuidado genérico
			if (!empty($new['care'])) {
				$item['title'] = 'Cuidado';
				$care = $new['care'];
				// intentar obtener imagen desde el cuidado
				if (!empty($care['id'])) {
					$c = Care::find($care['id']);
					if ($c && $c->imagen_url) {
						$imageUrl = $c->imagen_url;
					}
				}
				$careTypeText = null;
				if (!empty($care['tipo_cuidado_id'])) {
					$ct = CareType::find($care['tipo_cuidado_id']);
					$careTypeText = $ct ? ('Tipo: '.$ct->nombre) : null;
				}
				$item['details'][] = [
					'label' => 'Detalle',
					'value' => implode(' | ', array_filter([
						!empty($care['descripcion']) ? $care['descripcion'] : null,
						$careTypeText,
					])),
				];
			}

			// Cuidado de alimentación
			if (!empty($new['care_feeding'])) {
				$item['title'] = 'Alimentación';
				$cf = $new['care_feeding'];
				$tipo = isset($cf['feeding_type_id']) ? ($feedTypes[$cf['feeding_type_id']]->nombre) : null;
				$freq = isset($cf['feeding_frequency_id']) ? ($feedFreqs[$cf['feeding_frequency_id']]->nombre) : null;
				$portion = null;
				if (isset($cf['feeding_portion_id'])) {
					$p = $feedPortions[$cf['feeding_portion_id']] ?? null;
					$portion = $p ? ($p->cantidad.' '.$p->unidad) : null;
				}
				$item['details'][] = [
					'label' => 'Información',
					'value' => implode(' | ', array_filter([
						$tipo ? ('Tipo: '.$tipo) : null,
						$freq ? ('Frecuencia: '.$freq) : null,
						$portion ? ('Porción: '.$portion) : null,
					])),
				];
			}

			// Traslado
			if (!empty($new['transfer'])) {
				$item['title'] = 'Traslado';
				$tr = $new['transfer'];
				$personName = isset($tr['persona_id']) ? (Person::find($tr['persona_id'])->nombre) : null;
				$centerName = isset($tr['centro_id']) ? (Center::find($tr['centro_id'])->nombre) : null;
				$item['details'][] = [
					'label' => 'Información',
					'value' => implode(' | ', array_filter([
						$personName ? ('Persona: '.$personName) : null,
						$centerName ? ('Centro: '.$centerName) : null,
						isset($tr['primer_traslado']) ? ('Primer traslado: '.($tr['primer_traslado'] ? 'Sí' : 'No')) : null,
						!empty($tr['observaciones']) ? ('Obs: '.$tr['observaciones']) : null,
					])),
				];
			}

            // Liberación
            if (!empty($new['liberacion'])) {
                $item['title'] = 'Liberación';
                $lib = $new['liberacion'];
                $aprob = array_key_exists('aprobada', $lib) ? (bool)$lib['aprobada'] : null;
                $parts = [];
                if (!empty($lib['direccion'])) $parts[] = 'Dirección: '.$lib['direccion'];
                //if (!is_null($aprob)) $parts[] = 'Aprobada: '.($aprob ? 'Sí' : 'No');
                $item['details'][] = [
                    'label' => 'Información',
                    'value' => implode(' | ', $parts),
                ];
            }

			// Creación de Hoja de Vida / Animal
			if (!empty($new['animal']) || !empty($new['animal_file'])) {
				if (!empty($new['animal'])) {
					$item['title'] = 'Animal';
					$an = $new['animal'];
					$item['details'][] = [
						'label' => 'Información',
						'value' => implode(' | ', array_filter([
							!empty($an['nombre']) ? ('Nombre: '.$an['nombre']) : null,
							!empty($an['sexo']) ? ('Sexo: '.$an['sexo']) : null,
						])),
					];
				}
				if (!empty($new['animal_file'])) {
					$item['title'] = 'Creación de Hoja de Vida';
					$af = $new['animal_file'];
					// imagen de hoja de vida si existe
					if (!empty($af['id'])) {
						$afModel = AnimalFileModel::find($af['id']);
						if ($afModel && $afModel->imagen_url) {
							$imageUrl = $afModel->imagen_url;
						}
					}
					$estadoName = isset($af['estado_id']) ? (AnimalStatus::find($af['estado_id'])->nombre) : null;
					$tipoName = isset($af['tipo_id']) ? (AnimalType::find($af['tipo_id'])->nombre) : null;
					$espName = isset($af['especie_id']) ? (Species::find($af['especie_id'])->nombre) : null;
					$item['details'][] = [
						'label' => 'Detalle',
						'value' => implode(' | ', array_filter([
							$estadoName ? ('Estado: '.$estadoName) : null,
							$tipoName ? ('Tipo: '.$tipoName) : null,
							$espName ? ('Especie: '.$espName) : null,
						])),
					];
				}
			}

			// Observaciones
			if (!empty($h->observaciones)) {
				$obs = is_array($h->observaciones) ? ($h->observaciones['texto'] ?? json_encode($h->observaciones, JSON_UNESCAPED_UNICODE)) : $h->observaciones;
				$item['details'][] = [
					'label' => 'Observaciones',
					'value' => $obs,
				];
			}

			if ($imageUrl) {
				$item['image_url'] = $imageUrl;
			}

			$timeline[] = $item;
		}

		return $timeline;
	}
}


