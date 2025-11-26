<?php

namespace App\Http\Controllers\Transactions;

use App\Http\Controllers\Controller;
use App\Http\Requests\Transactions\CareProcessRequest;
use App\Models\AnimalFile;
use App\Models\CareType;
use App\Services\Animal\AnimalCareTransactionalService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class AnimalCareTransactionalController extends Controller
{
	public function __construct(
		private readonly AnimalCareTransactionalService $service
	) {
		$this->middleware('auth');
	}

	public function create(): View
	{
		$animalFiles = AnimalFile::with('animal')->orderByDesc('id')->get(['id','animal_id']);
		$careTypes = CareType::orderBy('nombre')->get(['id','nombre']);

		return view('transactions.animal.care.create', compact('animalFiles','careTypes'));
	}

	public function store(CareProcessRequest $request): RedirectResponse
	{
		$data = $request->validated();
		$image = $request->file('imagen');

		$this->service->registerCare($data, $image);

		return Redirect::route('cares.index')
			->with('success', 'Cuidado registrado correctamente (transaccional).');
	}
}






