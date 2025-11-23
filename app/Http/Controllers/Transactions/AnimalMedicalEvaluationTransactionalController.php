<?php

namespace App\Http\Controllers\Transactions;

use App\Http\Controllers\Controller;
use App\Http\Requests\Transactions\MedicalEvaluationProcessRequest;
use App\Models\AnimalFile;
use App\Models\AnimalStatus;
use App\Models\TreatmentType;
use App\Models\Veterinarian;
use App\Services\Animal\AnimalMedicalEvaluationTransactionalService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class AnimalMedicalEvaluationTransactionalController extends Controller
{
	public function __construct(
		private readonly AnimalMedicalEvaluationTransactionalService $service
	) {
		$this->middleware('auth');
	}

	public function create(): View
	{
		$animalFiles = AnimalFile::with('animal')->orderByDesc('id')->get(['id','animal_id','estado_id']);
		$treatmentTypes = TreatmentType::orderBy('nombre')->get(['id','nombre']);
		$veterinarians = Veterinarian::with('person')->where('aprobado', true)->orderBy('id')->get();
		$statuses = AnimalStatus::orderBy('nombre')->get(['id','nombre']);

		return view('transactions.animal.medical-evaluation.create', compact(
			'animalFiles',
			'treatmentTypes',
			'veterinarians',
			'statuses'
		));
	}

	public function store(MedicalEvaluationProcessRequest $request): RedirectResponse
	{
		$data = $request->validated();
		$image = $request->file('imagen');

		$this->service->registerEvaluation($data, $image);

		return Redirect::route('animal-histories.index')
			->with('success', 'Evaluación médica registrada y estado actualizado (transaccional).');
	}
}


