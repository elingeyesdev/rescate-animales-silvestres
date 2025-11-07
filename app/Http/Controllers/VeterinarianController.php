<?php

namespace App\Http\Controllers;

use App\Models\Veterinarian;
use App\Models\Person;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\VeterinarianRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class VeterinarianController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $veterinarians = Veterinarian::with('person')->paginate();

        return view('veterinarian.index', compact('veterinarians'))
            ->with('i', ($request->input('page', 1) - 1) * $veterinarians->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $veterinarian = new Veterinarian();
        $people = Person::orderBy('nombre')->get(['id','nombre']);
        return view('veterinarian.create', compact('veterinarian','people'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(VeterinarianRequest $request): RedirectResponse
    {
        $data = $request->validated();
        if ($request->hasFile('cv')) {
            $data['cv_documentado'] = $request->file('cv')->store('cv', 'public');
        }
        Veterinarian::create($data);

        return Redirect::route('veterinarians.index')
            ->with('success', 'Veterinario creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $veterinarian = Veterinarian::find($id);

        return view('veterinarian.show', compact('veterinarian'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $veterinarian = Veterinarian::find($id);
        $people = Person::orderBy('nombre')->get(['id','nombre']);
        return view('veterinarian.edit', compact('veterinarian','people'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(VeterinarianRequest $request, Veterinarian $veterinarian): RedirectResponse
    {
        $data = $request->validated();
        if ($request->hasFile('cv')) {
            $data['cv_documentado'] = $request->file('cv')->store('cv', 'public');
        }
        $veterinarian->update($data);

        return Redirect::route('veterinarians.index')
            ->with('success', 'Veterinario actualizado correctamente');
    }

    public function destroy($id): RedirectResponse
    {
        Veterinarian::find($id)->delete();

        return Redirect::route('veterinarians.index')
            ->with('success', 'Veterinario eliminado correctamente');
    }
}
