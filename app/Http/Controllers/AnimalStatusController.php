<?php

namespace App\Http\Controllers;

use App\Models\AnimalStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\AnimalStatusRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class AnimalStatusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $animalStatuses = AnimalStatus::paginate();

        return view('animal-status.index', compact('animalStatuses'))
            ->with('i', ($request->input('page', 1) - 1) * $animalStatuses->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $animalStatus = new AnimalStatus();

        return view('animal-status.create', compact('animalStatus'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AnimalStatusRequest $request): RedirectResponse
    {
        AnimalStatus::create($request->validated());

        return Redirect::route('animal-statuses.index')
            ->with('success', 'Estado creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $animalStatus = AnimalStatus::find($id);

        return view('animal-status.show', compact('animalStatus'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $animalStatus = AnimalStatus::find($id);

        return view('animal-status.edit', compact('animalStatus'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AnimalStatusRequest $request, AnimalStatus $animalStatus): RedirectResponse
    {
        $animalStatus->update($request->validated());

        return Redirect::route('animal-statuses.index')
            ->with('success', 'Estado de Animal actualizado exitosamente');
    }

    public function destroy($id): RedirectResponse
    {
        AnimalStatus::find($id)->delete();

        return Redirect::route('animal-statuses.index')
            ->with('success', 'Estado eliminado correctamente');
    }
}
