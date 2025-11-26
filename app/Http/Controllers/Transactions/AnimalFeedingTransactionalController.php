<?php

namespace App\Http\Controllers\Transactions;

use App\Http\Controllers\Controller;
use App\Http\Requests\Transactions\FeedingProcessRequest;
use App\Models\AnimalFile;
use App\Models\FeedingFrequency;
use App\Models\FeedingPortion;
use App\Models\FeedingType;
use App\Services\Animal\AnimalFeedingTransactionalService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class AnimalFeedingTransactionalController extends Controller
{
	public function __construct(
		private readonly AnimalFeedingTransactionalService $service
	) {
		$this->middleware('auth');
	}

	public function create(): View
	{
		$animalFiles = AnimalFile::with('animal')
			->whereDoesntHave('release')
			->orderByDesc('id')
			->get(['id','animal_id']);
		$feedingTypeOptions = FeedingType::orderBy('nombre')->pluck('nombre', 'id');
		$feedingFrequencyOptions = FeedingFrequency::orderBy('nombre')->pluck('nombre', 'id');
		$feedingPortionOptions = FeedingPortion::orderBy('cantidad')->get()->mapWithKeys(function ($portion) {
			return [$portion->id => $portion->cantidad.' '.$portion->unidad];
		});

		return view('transactions.animal.feeding.create', compact(
			'animalFiles',
			'feedingTypeOptions',
			'feedingFrequencyOptions',
			'feedingPortionOptions'
		));
	}

	public function store(FeedingProcessRequest $request): RedirectResponse
	{
		$this->service->registerFeeding($request->validated());

		return Redirect::route('care-feedings.index')
			->with('success', 'Alimentación registrada correctamente.');
	}
}


