<?php

namespace App\Http\Controllers;

use App\Models\Adoption;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\AdoptionRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class AdoptionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $adoptions = Adoption::paginate();

        return view('adoption.index', compact('adoptions'))
            ->with('i', ($request->input('page', 1) - 1) * $adoptions->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $adoption = new Adoption();

        return view('adoption.create', compact('adoption'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AdoptionRequest $request): RedirectResponse
    {
        Adoption::create($request->validated());

        return Redirect::route('adoptions.index')
            ->with('success', 'Adoption created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $adoption = Adoption::find($id);

        return view('adoption.show', compact('adoption'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $adoption = Adoption::find($id);

        return view('adoption.edit', compact('adoption'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AdoptionRequest $request, Adoption $adoption): RedirectResponse
    {
        $adoption->update($request->validated());

        return Redirect::route('adoptions.index')
            ->with('success', 'Adoption updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        Adoption::find($id)->delete();

        return Redirect::route('adoptions.index')
            ->with('success', 'Adoption deleted successfully');
    }
}
