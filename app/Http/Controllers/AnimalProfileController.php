<?php

namespace App\Http\Controllers;

use App\Models\AnimalProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\AnimalProfileRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class AnimalProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $animalProfiles = AnimalProfile::paginate();

        return view('animal-profile.index', compact('animalProfiles'))
            ->with('i', ($request->input('page', 1) - 1) * $animalProfiles->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $animalProfile = new AnimalProfile();

        return view('animal-profile.create', compact('animalProfile'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AnimalProfileRequest $request): RedirectResponse
    {
        AnimalProfile::create($request->validated());

        return Redirect::route('animal-profiles.index')
            ->with('success', 'AnimalProfile created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $animalProfile = AnimalProfile::find($id);

        return view('animal-profile.show', compact('animalProfile'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $animalProfile = AnimalProfile::find($id);

        return view('animal-profile.edit', compact('animalProfile'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AnimalProfileRequest $request, AnimalProfile $animalProfile): RedirectResponse
    {
        $animalProfile->update($request->validated());

        return Redirect::route('animal-profiles.index')
            ->with('success', 'AnimalProfile updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        AnimalProfile::find($id)->delete();

        return Redirect::route('animal-profiles.index')
            ->with('success', 'AnimalProfile deleted successfully');
    }
}
