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
	}

	public function index(\Illuminate\Http\Request $request): View
    {
        $order = $request->get('order') === 'asc' ? 'asc' : 'desc';
        $histories = $this->timelineService->latestPerAnimalFileOrdered($order);

        return view('animal-history.index', compact('histories'))
            ->with('i', ($request->input('page', 1) - 1) * $histories->perPage());
    }

	public function show(AnimalHistory $animalHistory): View
	{
		$animalHistory->loadMissing(['animalFile.animal']);
        $timeline = $animalHistory->animal_file_id
            ? $this->timelineService->buildForAnimalFile($animalHistory->animal_file_id)
            : [];

        return view('animal-history.show', [
            'animalHistory' => $animalHistory,
            'timeline' => $timeline,
        ]);
	}
}


