<?php

namespace App\Http\Controllers;

use App\Models\Care;
use App\Models\AnimalFile;
use App\Models\CareType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\CareRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class CareController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $cares = Care::with(['animalFile.animal','careType'])->paginate();

        return view('care.index', compact('cares'))
            ->with('i', ($request->input('page', 1) - 1) * $cares->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $care = new Care();
        // Join animals to get displayable name from animals.nombre
        $animalFiles = AnimalFile::query()
            ->join('animals', 'animal_files.animal_id', '=', 'animals.id')
            ->orderBy('animals.nombre')
            ->get([
                'animal_files.id as id',
                'animals.nombre as nombre',
            ]);
        $careTypes = CareType::orderBy('nombre')->get(['id','nombre']);

        return view('care.create', compact('care','animalFiles','careTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CareRequest $request): RedirectResponse
    {
        $data = $request->validated();
        if ($request->hasFile('imagen')) {
            $path = $request->file('imagen')->store('evidencias/cares', 'public');
            // Guardar ruta relativa; la vista resolverá URL pública
            $data['imagen_url'] = $path;
        }
        Care::create($data);

        return Redirect::route('cares.index')
            ->with('success', 'Cuidado creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $care = Care::find($id);

        return view('care.show', compact('care'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $care = Care::find($id);
        // Join animals to get displayable name from animals.nombre
        $animalFiles = AnimalFile::query()
            ->join('animals', 'animal_files.animal_id', '=', 'animals.id')
            ->orderBy('animals.nombre')
            ->get([
                'animal_files.id as id',
                'animals.nombre as nombre',
            ]);
        $careTypes = CareType::orderBy('nombre')->get(['id','nombre']);

        return view('care.edit', compact('care','animalFiles','careTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CareRequest $request, Care $care): RedirectResponse
    {
        $data = $request->validated();
        if ($request->hasFile('imagen')) {
            $path = $request->file('imagen')->store('evidencias/cares', 'public');
            // Guardar ruta relativa; la vista resolverá URL pública
            $data['imagen_url'] = $path;
        }
        $care->update($data);

        return Redirect::route('cares.index')
            ->with('success', 'Cuidado actualizado correctamente');
    }

    public function destroy($id): RedirectResponse
    {
        Care::find($id)->delete();

        return Redirect::route('cares.index')
            ->with('success', 'Cuidado eliminado correctamente');
    }
}
