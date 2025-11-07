<?php

namespace App\Http\Controllers;

use App\Models\AnimalFile;
use App\Models\Species;
use App\Models\Breed;
use App\Models\AnimalStatus;
use App\Models\AnimalType;
use App\Models\Report;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\AnimalFileRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class AnimalFileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $animalFiles = AnimalFile::paginate();

        return view('animal-file.index', compact('animalFiles'))
            ->with('i', ($request->input('page', 1) - 1) * $animalFiles->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $animalFile = new AnimalFile();
        $species = Species::orderBy('nombre')->get(['id','nombre']);
        $animalStatuses = AnimalStatus::orderBy('nombre')->get(['id','nombre']);
        $animalTypes = AnimalType::orderBy('nombre')->get(['id','nombre']);
        $reports = Report::orderByDesc('id')->get(['id']);

        return view('animal-file.create', compact('animalFile','species','animalStatuses','animalTypes','reports'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AnimalFileRequest $request): RedirectResponse
    {
        AnimalFile::create($request->validated());

        return Redirect::route('animal-files.index')
            ->with('success', 'AnimalFile created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $animalFile = AnimalFile::find($id);

        return view('animal-file.show', compact('animalFile'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $animalFile = AnimalFile::find($id);
        $species = Species::orderBy('nombre')->get(['id','nombre']);
        $animalStatuses = AnimalStatus::orderBy('nombre')->get(['id','nombre']);
        $animalTypes = AnimalType::orderBy('nombre')->get(['id','nombre']);
        $reports = Report::orderByDesc('id')->get(['id']);
        $breeds = $animalFile?->especie_id ? Breed::where('especie_id', $animalFile->especie_id)->orderBy('nombre')->get(['id','nombre']) : collect();

        return view('animal-file.edit', compact('animalFile','species','animalStatuses','animalTypes','reports','breeds'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AnimalFileRequest $request, AnimalFile $animalFile): RedirectResponse
    {
        $animalFile->update($request->validated());

        return Redirect::route('animal-files.index')
            ->with('success', 'AnimalFile updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        AnimalFile::find($id)->delete();

        return Redirect::route('animal-files.index')
            ->with('success', 'AnimalFile deleted successfully');
    }
}
