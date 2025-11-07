<?php

namespace App\Http\Controllers;

use App\Models\Veterinarian;
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
        $veterinarians = Veterinarian::paginate();

        return view('veterinarian.index', compact('veterinarians'))
            ->with('i', ($request->input('page', 1) - 1) * $veterinarians->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $veterinarian = new Veterinarian();

        return view('veterinarian.create', compact('veterinarian'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(VeterinarianRequest $request): RedirectResponse
    {
        Veterinarian::create($request->validated());

        return Redirect::route('veterinarians.index')
            ->with('success', 'Veterinarian created successfully.');
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

        return view('veterinarian.edit', compact('veterinarian'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(VeterinarianRequest $request, Veterinarian $veterinarian): RedirectResponse
    {
        $veterinarian->update($request->validated());

        return Redirect::route('veterinarians.index')
            ->with('success', 'Veterinarian updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        Veterinarian::find($id)->delete();

        return Redirect::route('veterinarians.index')
            ->with('success', 'Veterinarian deleted successfully');
    }
}
