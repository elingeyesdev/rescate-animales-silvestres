<?php

namespace App\Http\Controllers;

use App\Models\AnimalFile;
use App\Models\AnimalType;
use App\Models\Species;
use App\Models\AnimalStatus;
use App\Models\Report;
use App\Models\Animal;
use App\Models\Breed;
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

        // Preseleccionar tipo "Silvestre" si existe
        $defaultTypeId = $animalTypes->firstWhere('nombre', 'Silvestre')?->id;
        if ($defaultTypeId && empty($animalFile->tipo_id)) {
            $animalFile->tipo_id = $defaultTypeId;
        }
        // Preseleccionar Especie "Desconocido" si existe
        $unknownSpeciesId = Species::whereRaw('LOWER(nombre) = ?', ['desconocido'])->value('id');
        if ($unknownSpeciesId && empty($animalFile->especie_id)) {
            $animalFile->especie_id = $unknownSpeciesId;
        }
        // Preseleccionar Estado "En recuperación" si existe
        $recoveryStatusId = AnimalStatus::whereRaw('LOWER(nombre) = ?', ['en recuperación'])->value('id');
        if ($recoveryStatusId && empty($animalFile->estado_id)) {
            $animalFile->estado_id = $recoveryStatusId;
        }

        return view('animal-file.create', compact('animalFile','animalTypes','species','animalStatuses','animals'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AnimalFileRequest $request): RedirectResponse
    {
        try {
            $data = $request->validated();
            // animal_nombre is for updating related Animal's nombre, not a column in animal_files
            $animalNombre = $data['animal_nombre'] ?? null;
            unset($data['animal_nombre']);
            if ($request->hasFile('imagen')) {
                $path = $request->file('imagen')->store('animal_files', 'public');
                $data['imagen_url'] = $path;
            }
            $animalFile = AnimalFile::create($data);
            if (!empty($animalNombre)) {
                $animal = Animal::find($animalFile->animal_id);
                if ($animal) {
                    $animal->update(['nombre' => $animalNombre]);
                }
            }

            return Redirect::route('animal-files.index')
                ->with('success', 'Hoja del Animal creada correctamente.');
        } catch (\Throwable $e) {
            return Redirect::back()
                ->withInput()
                ->withErrors(['general' => 'No se pudo crear la hoja del animal: ' . $e->getMessage()]);
        }
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
        $breeds = $animalFile?->especie_id
            ? Breed::where('especie_id', $animalFile->especie_id)->orderBy('nombre')->get(['id','nombre'])
            : collect();

        return view('animal-file.edit', compact('animalFile','animalTypes','species','animalStatuses','animals','breeds'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AnimalFileRequest $request, AnimalFile $animalFile): RedirectResponse
    {
        try {
            $data = $request->validated();
            // animal_nombre is for updating related Animal's nombre, not a column in animal_files
            $animalNombre = $data['animal_nombre'] ?? null;
            unset($data['animal_nombre']);
            if ($request->hasFile('imagen')) {
                $path = $request->file('imagen')->store('animal_files', 'public');
                $data['imagen_url'] = $path;
            }
            $animalFile->update($data);
            if (!empty($animalNombre)) {
                $animalId = $data['animal_id'] ?? $animalFile->animal_id;
                $animal = Animal::find($animalId);
                if ($animal) {
                    $animal->update(['nombre' => $animalNombre]);
                }
            }

            return Redirect::route('animal-files.index')
                ->with('success', 'Hoja del Animal actualizada exitosamente');
        } catch (\Throwable $e) {
            return Redirect::back()
                ->withInput()
                ->withErrors(['general' => 'No se pudo actualizar la hoja del animal: ' . $e->getMessage()]);
        }
    }

    public function destroy($id): RedirectResponse
    {
        AnimalFile::find($id)->delete();

        return Redirect::route('animal-files.index')
            ->with('success', 'Hoja del Animal eliminada correctamente');
    }
}
