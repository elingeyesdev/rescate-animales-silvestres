<?php

namespace App\Http\Controllers;

use App\Models\AnimalType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\AnimalTypeRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class AnimalTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $animalTypes = AnimalType::paginate();

        return view('animal-type.index', compact('animalTypes'))
            ->with('i', ($request->input('page', 1) - 1) * $animalTypes->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $animalType = new AnimalType();

        return view('animal-type.create', compact('animalType'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AnimalTypeRequest $request): RedirectResponse
    {
        AnimalType::create($request->validated());

        return Redirect::route('animal-types.index')
            ->with('success', 'AnimalType created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $animalType = AnimalType::find($id);

        return view('animal-type.show', compact('animalType'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $animalType = AnimalType::find($id);

        return view('animal-type.edit', compact('animalType'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AnimalTypeRequest $request, AnimalType $animalType): RedirectResponse
    {
        $animalType->update($request->validated());

        return Redirect::route('animal-types.index')
            ->with('success', 'AnimalType updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        AnimalType::find($id)->delete();

        return Redirect::route('animal-types.index')
            ->with('success', 'AnimalType deleted successfully');
    }
}
