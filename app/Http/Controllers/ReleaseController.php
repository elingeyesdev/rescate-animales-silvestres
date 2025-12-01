<?php

namespace App\Http\Controllers;

use App\Models\Release;
use App\Models\AnimalFile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\ReleaseRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Services\Animal\AnimalReleaseTransactionalService;

class ReleaseController extends Controller
{
    public function __construct(
        private readonly AnimalReleaseTransactionalService $releaseService
    ) {
        // Cualquiera puede ver liberaciones (index, show) sin autenticación
        // Veterinarios, admin/encargado pueden crear, editar, actualizar y eliminar
        $this->middleware('auth')->only(['create', 'store', 'edit', 'update', 'destroy']);
        $this->middleware('role:veterinario|admin|encargado')->only(['create', 'store', 'edit', 'update', 'destroy']);
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Release::with(['animalFile.animal', 'animalFile.species', 'animalFile.animalStatus']);

        // Filtros
        if ($request->filled('nombre_animal')) {
            $query->whereHas('animalFile.animal', function($q) use ($request) {
                $q->where('nombre', 'like', '%'.$request->input('nombre_animal').'%');
            });
        }

        if ($request->filled('especie_id')) {
            $query->whereHas('animalFile', function($q) use ($request) {
                $q->where('especie_id', $request->input('especie_id'));
            });
        }

        // Filtro de aprobación eliminado - todas las liberaciones están aprobadas

        if ($request->filled('fecha_desde')) {
            $query->whereDate('created_at', '>=', $request->input('fecha_desde'));
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('created_at', '<=', $request->input('fecha_hasta'));
        }

        $releases = $query->orderBy('created_at', 'desc')->paginate(12)->withQueryString();

        // Datos para filtros
        $species = \App\Models\Species::orderBy('nombre')->get();

        return view('release.index', compact('releases', 'species'))
            ->with('i', ($request->input('page', 1) - 1) * $releases->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $release = new Release();
        $animalFiles = AnimalFile::with(['animal.report.person','animalStatus'])
            ->join('animal_statuses', 'animal_files.estado_id', '=', 'animal_statuses.id')
            ->leftJoin('releases', 'releases.animal_file_id', '=', 'animal_files.id')
            ->join('animals', 'animal_files.animal_id', '=', 'animals.id')
            ->whereRaw('LOWER(animal_statuses.nombre) = ?', ['estable'])
            ->whereNull('releases.animal_file_id')
            ->orderBy('animals.nombre')
            ->get(['animal_files.id', 'animal_files.animal_id', 'animal_files.estado_id', 'animal_files.imagen_url']);

        // Datos para cards de Paso 1
        $afCards = $animalFiles->map(function ($af) {
            return [
                'id' => $af->id,
                'img' => $af->imagen_url ? asset('storage/'.$af->imagen_url) : null,
                'status' => $af->animalStatus?->nombre,
                'reporter' => $af->animal?->report?->person?->nombre,
                'name' => ($af->animal?->nombre ?? ('#' . $af->animal?->id)),
            ];
        })->values()->toArray();

        return view('release.create', compact('release','animalFiles','afCards'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ReleaseRequest $request): RedirectResponse
    {
        try {
            $data = $request->validated();
            $image = $request->file('imagen');
            $this->releaseService->create($data, $image);
        } catch (\DomainException $e) {
            return Redirect::back()->withInput()->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            return Redirect::back()->withInput()->with('error', 'No se pudo registrar la liberación: '.$e->getMessage());
        }

        return Redirect::route('releases.index')
            ->with('success', 'Liberación creada correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $release = Release::with(['animalFile.animal', 'animalFile.species', 'animalFile.animalStatus'])->findOrFail($id);

        return view('release.show', compact('release'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $release = Release::find($id);
        $animalFiles = AnimalFile::with(['animal.report.person','animalStatus'])
            ->join('animal_statuses', 'animal_files.estado_id', '=', 'animal_statuses.id')
            ->leftJoin('releases', 'releases.animal_file_id', '=', 'animal_files.id')
            ->join('animals', 'animal_files.animal_id', '=', 'animals.id')
            ->where(function($q) use ($release) {
                $q->whereNull('releases.animal_file_id')
                  ->orWhere('releases.id', $release->id);
            })
            ->where(function($q) use ($release) {
                // Permitir animales en estado "Estable" o el actualmente asociado a la liberación
                $q->whereRaw('LOWER(animal_statuses.nombre) = ?', ['estable'])
                  ->orWhere('releases.id', $release->id);
            })
            ->orderBy('animals.nombre')
            ->get(['animal_files.id', 'animal_files.animal_id', 'animal_files.estado_id', 'animal_files.imagen_url']);

        // Datos para cards de Paso 1
        $afCards = $animalFiles->map(function ($af) {
            return [
                'id' => $af->id,
                'img' => $af->imagen_url ? asset('storage/'.$af->imagen_url) : null,
                'status' => $af->animalStatus?->nombre,
                'reporter' => $af->animal?->report?->person?->nombre,
                'name' => ($af->animal?->nombre ?? ('#' . $af->animal?->id)),
            ];
        })->values()->toArray();

        return view('release.edit', compact('release','animalFiles','afCards'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ReleaseRequest $request, Release $release): RedirectResponse
    {
        $data = $request->validated();
        $image = $request->file('imagen');
        
        if ($image) {
            // Eliminar imagen anterior si existe
            if ($release->imagen_url) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($release->imagen_url);
            }
            $data['imagen_url'] = $image->store('evidencias/releases', 'public');
        }
        
        // Las liberaciones siempre están aprobadas (solo administradores pueden editarlas)
        $data['aprobada'] = true;
        
        $release->update($data);

        return Redirect::route('releases.index')
            ->with('success', 'Liberación actualizada correctamente');
    }

    public function destroy($id): RedirectResponse
    {
        Release::find($id)->delete();

        return Redirect::route('releases.index')
            ->with('success', 'Liberación eliminada correctamente');
    }
}
