<?php

namespace App\Http\Controllers;

use App\Models\Species;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\SpeciesRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class SpeciesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $species = Species::paginate();

        return view('species.index', compact('species'))
            ->with('i', ($request->input('page', 1) - 1) * $species->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $species = new Species();

        return view('species.create', compact('species'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SpeciesRequest $request): RedirectResponse
    {
        Species::create($request->validated());

        return Redirect::route('species.index')
            ->with('success', 'Species created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $species = Species::find($id);

        return view('species.show', compact('species'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $species = Species::find($id);

        return view('species.edit', compact('species'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SpeciesRequest $request, Species $species): RedirectResponse
    {
        $species->update($request->validated());

        return Redirect::route('species.index')
            ->with('success', 'Species updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        Species::find($id)->delete();

        return Redirect::route('species.index')
            ->with('success', 'Species deleted successfully');
    }
}
