<?php

namespace App\Http\Controllers;

use App\Models\AnimalCondition;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\AnimalConditionRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class AnimalConditionController extends Controller
{
	public function __construct()
	{
		$this->middleware('auth');
	}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $animalConditions = AnimalCondition::orderBy('nombre')->paginate();

        return view('animal-condition.index', compact('animalConditions'))
            ->with('i', ($request->input('page', 1) - 1) * $animalConditions->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $animalCondition = new AnimalCondition();

        return view('animal-condition.create', compact('animalCondition'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AnimalConditionRequest $request): RedirectResponse
    {
        AnimalCondition::create($request->validated());

        return Redirect::route('animal-conditions.index')
            ->with('success', 'Condición creada correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $animalCondition = AnimalCondition::find($id);

        return view('animal-condition.show', compact('animalCondition'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $animalCondition = AnimalCondition::find($id);

        return view('animal-condition.edit', compact('animalCondition'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AnimalConditionRequest $request, AnimalCondition $animalCondition): RedirectResponse
    {
        $animalCondition->update($request->validated());

        return Redirect::route('animal-conditions.index')
            ->with('success', 'Condición actualizada correctamente');
    }

    public function destroy($id): RedirectResponse
    {
        AnimalCondition::find($id)->delete();

        return Redirect::route('animal-conditions.index')
            ->with('success', 'Condición eliminada correctamente');
    }
}


