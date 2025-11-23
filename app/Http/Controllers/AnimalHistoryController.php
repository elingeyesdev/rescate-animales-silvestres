<?php

namespace App\Http\Controllers;

use App\Models\AnimalHistory;
use Illuminate\View\View;

class AnimalHistoryController extends Controller
{
	public function __construct()
	{
		$this->middleware('auth');
	}

	public function index(): View
	{
		$histories = AnimalHistory::query()
			->with(['animalFile.animal'])
			->orderByDesc('id')
			->paginate();

		return view('animal-history.index', compact('histories'))
			->with('i', (request()->input('page', 1) - 1) * $histories->perPage());
	}

	public function show(AnimalHistory $animalHistory): View
	{
		$animalHistory->loadMissing(['animalFile.animal']);
		return view('animal-history.show', compact('animalHistory'));
	}
}


