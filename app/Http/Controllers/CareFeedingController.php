<?php

namespace App\Http\Controllers;

use App\Models\CareFeeding;
use App\Models\Care;
use App\Models\FeedingType;
use App\Models\FeedingFrequency;
use App\Models\FeedingPortion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\CareFeedingRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Carbon;

class CareFeedingController extends Controller
{
    public function __construct()
    {
        // Debe estar autenticado
        $this->middleware('auth');
        // Cuidadores, veterinarios, encargados y administradores pueden ver y crear registros de alimentación
        $this->middleware('role:cuidador|veterinario|encargado|admin');
        // Solo encargados y administradores pueden editar/eliminar registros existentes
        $this->middleware('role:encargado|admin')->only(['edit','update','destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $careFeedings = CareFeeding::with([
            'care.animalFile.animal',
            'care.animalFile.species',
            'care.animalFile.animalStatus',
            'feedingType',
            'feedingFrequency',
            'feedingPortion'
        ])
            ->orderByDesc('id')
            ->get();

        // Agrupar alimentaciones por animal_file_id a través de care->hoja_animal_id
        $groupedFeedings = $careFeedings->groupBy(function($careFeeding) {
            return $careFeeding->care?->hoja_animal_id ?? 'sin_animal';
        });

        return view('care-feeding.index', compact('groupedFeedings'))
            ->with('i', 0);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $careFeeding = new CareFeeding();

		$careOptions = Care::orderByDesc('id')->get()->mapWithKeys(function (Care $care) {
			$date = isset($care->fecha) ? Carbon::parse($care->fecha)->format('d/m/y') : null;
			$label = 'Cuidado N°'.$care->id.($date ? ' - '.$date : '');
			return [$care->id => $label];
		});
		$feedingTypeOptions = FeedingType::orderBy('nombre')->pluck('nombre', 'id');
		$feedingFrequencyOptions = FeedingFrequency::orderBy('nombre')->pluck('nombre', 'id');
		$feedingPortionOptions = FeedingPortion::orderBy('cantidad')->get()->mapWithKeys(function (FeedingPortion $portion) {
			return [$portion->id => $portion->cantidad.' '.$portion->unidad];
		});

		return view('care-feeding.create', compact(
			'careFeeding',
			'careOptions',
			'feedingTypeOptions',
			'feedingFrequencyOptions',
			'feedingPortionOptions'
		));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CareFeedingRequest $request): RedirectResponse
    {
        CareFeeding::create($request->validated());

        return Redirect::route('care-feedings.index')
            ->with('success', 'Cuidado de alimentación creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $careFeeding = CareFeeding::find($id);

        return view('care-feeding.show', compact('careFeeding'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $careFeeding = CareFeeding::find($id);

		$careOptions = Care::orderByDesc('id')->get()->mapWithKeys(function (Care $care) {
			$date = isset($care->fecha) ? Carbon::parse($care->fecha)->format('d/m/y') : null;
			$label = 'Cuidado N°'.$care->id.($date ? ' - '.$date : '');
			return [$care->id => $label];
		});
		$feedingTypeOptions = FeedingType::orderBy('nombre')->pluck('nombre', 'id');
		$feedingFrequencyOptions = FeedingFrequency::orderBy('nombre')->pluck('nombre', 'id');
		$feedingPortionOptions = FeedingPortion::orderBy('cantidad')->get()->mapWithKeys(function (FeedingPortion $portion) {
			return [$portion->id => $portion->cantidad.' '.$portion->unidad];
		});

		return view('care-feeding.edit', compact(
			'careFeeding',
			'careOptions',
			'feedingTypeOptions',
			'feedingFrequencyOptions',
			'feedingPortionOptions'
		));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CareFeedingRequest $request, CareFeeding $careFeeding): RedirectResponse
    {
        $careFeeding->update($request->validated());

        return Redirect::route('care-feedings.index')
            ->with('success', 'Cuidado de alimentación actualizado correctamente');
    }

    public function destroy($id): RedirectResponse
    {
        CareFeeding::find($id)->delete();

        return Redirect::route('care-feedings.index')
            ->with('success', 'Cuidado de alimentación eliminado correctamente');
    }
}
