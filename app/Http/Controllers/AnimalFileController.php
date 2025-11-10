<?php

namespace App\Http\Controllers;

use App\Models\AnimalFile;
use App\Models\AnimalType;
use App\Models\Species;
use App\Models\AnimalStatus;
use App\Models\Report;
use App\Models\Animal;
use App\Models\Breed;
use App\Models\Adoption;
use App\Models\Release;
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
        $animalFiles = AnimalFile::with(['animalType','species','animalStatus','breed','animal.report','adoption','release'])
            ->paginate();

        return view('animal-file.index', compact('animalFiles'))
            ->with('i', ($request->input('page', 1) - 1) * $animalFiles->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $animalFile = new AnimalFile();
        $animalTypes = AnimalType::orderBy('nombre')->get(['id','nombre']);
        $species = Species::orderBy('nombre')->get(['id','nombre']);
        $animalStatuses = AnimalStatus::orderBy('nombre')->get(['id','nombre']);
        $animals = Animal::orderByDesc('id')->get(['id','nombre']);
        $adoptions = Adoption::orderByDesc('id')->get(['id']);
        $releases = Release::orderByDesc('id')->get(['id']);

        return view('animal-file.create', compact('animalFile','animalTypes','species','animalStatuses','animals','adoptions','releases'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AnimalFileRequest $request): RedirectResponse
    {
        $data = $request->validated();
        if ($request->hasFile('imagen')) {
            $path = $request->file('imagen')->store('animal_files', 'public');
            $data['imagen_url'] = $path;
        }
        AnimalFile::create($data);

        return Redirect::route('animal-files.index')
            ->with('success', 'Hoja Animal creada correctamente.');
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
        $animalTypes = AnimalType::orderBy('nombre')->get(['id','nombre']);
        $species = Species::orderBy('nombre')->get(['id','nombre']);
        $animalStatuses = AnimalStatus::orderBy('nombre')->get(['id','nombre']);
        $animals = Animal::orderByDesc('id')->get(['id','nombre']);
        $adoptions = Adoption::orderByDesc('id')->get(['id']);
        $releases = Release::orderByDesc('id')->get(['id']);
        $breeds = $animalFile?->especie_id
            ? Breed::where('especie_id', $animalFile->especie_id)->orderBy('nombre')->get(['id','nombre'])
            : collect();

        return view('animal-file.edit', compact('animalFile','animalTypes','species','animalStatuses','animals','adoptions','releases','breeds'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AnimalFileRequest $request, AnimalFile $animalFile): RedirectResponse
    {
        $data = $request->validated();
        if ($request->hasFile('imagen')) {
            $path = $request->file('imagen')->store('animal_files', 'public');
            $data['imagen_url'] = $path;
        }
        $animalFile->update($data);

        return Redirect::route('animal-files.index')
            ->with('success', 'Hoja de Animal actualizada exitosamente');
    }

    public function destroy($id): RedirectResponse
    {
        AnimalFile::find($id)->delete();

        return Redirect::route('animal-files.index')
            ->with('success', 'Hoja Animal eliminada correctamente');
    }
}
