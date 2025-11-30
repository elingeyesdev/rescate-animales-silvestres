<?php

namespace App\Http\Controllers;

use App\Models\AnimalHistory;
use App\Services\History\AnimalHistoryTimelineService;
use Illuminate\View\View;

class AnimalHistoryController extends Controller
{
	public function __construct(
		private readonly AnimalHistoryTimelineService $timelineService
	)
	{
		$this->middleware('auth');
        // Historial visible para cuidadores, rescatistas, veterinarios, encargados y administradores
        $this->middleware('role:cuidador|rescatista|veterinario|encargado|admin');
	}

	public function index(\Illuminate\Http\Request $request): View
    {
        $order = $request->get('order') === 'asc' ? 'asc' : 'desc';
        $histories = $this->timelineService->latestPerAnimalFileOrdered($order);

        return view('animal-history.index', compact('histories'))
            ->with('i', ($request->input('page', 1) - 1) * $histories->perPage());
    }

	public function show($id): View
	{
		// Intentar encontrar por ID de AnimalHistory primero
		$animalHistory = AnimalHistory::find($id);
		
		// Si no se encuentra, asumir que es un animal_file_id
		if (!$animalHistory) {
			$animalFileId = (int) $id;
			
			// Verificar que existe el AnimalFile
			$animalFile = \App\Models\AnimalFile::find($animalFileId);
			if (!$animalFile) {
				abort(404, 'Animal file not found');
			}
			
			// Buscar un historial existente para este animal_file_id
			$animalHistory = AnimalHistory::where('animal_file_id', $animalFileId)
				->orderByDesc('id')
				->first();

			// Si no existe, crear uno temporal solo para mostrar (no se guarda)
			if (!$animalHistory) {
				$animalHistory = new AnimalHistory();
				$animalHistory->animal_file_id = $animalFileId;
				$animalHistory->id = 0; // ID temporal
			}
		}

		$animalHistory->loadMissing(['animalFile.animal']);
        $animalFileId = $animalHistory->animal_file_id;
        
        $timeline = $animalFileId
            ? $this->timelineService->buildForAnimalFile($animalFileId)
            : [];
        $mapRoute = $animalFileId
            ? $this->timelineService->buildLocationRoute($animalFileId)
            : ['points' => []];

        return view('animal-history.show', [
            'animalHistory' => $animalHistory,
            'timeline' => $timeline,
            'mapRoute' => $mapRoute,
        ]);
	}
}


