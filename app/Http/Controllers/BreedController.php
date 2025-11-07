<?php

namespace App\Http\Controllers;

use App\Models\Breed;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\BreedRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class BreedController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $breeds = Breed::paginate();

        return view('breed.index', compact('breeds'))
            ->with('i', ($request->input('page', 1) - 1) * $breeds->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $breed = new Breed();

        return view('breed.create', compact('breed'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BreedRequest $request): RedirectResponse
    {
        Breed::create($request->validated());

        return Redirect::route('breeds.index')
            ->with('success', 'Breed created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $breed = Breed::find($id);

        return view('breed.show', compact('breed'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $breed = Breed::find($id);

        return view('breed.edit', compact('breed'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BreedRequest $request, Breed $breed): RedirectResponse
    {
        $breed->update($request->validated());

        return Redirect::route('breeds.index')
            ->with('success', 'Breed updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        Breed::find($id)->delete();

        return Redirect::route('breeds.index')
            ->with('success', 'Breed deleted successfully');
    }
}
