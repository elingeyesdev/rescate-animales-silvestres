<?php

namespace App\Http\Controllers;

use App\Models\Rescuer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\RescuerRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class RescuerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $rescuers = Rescuer::paginate();

        return view('rescuer.index', compact('rescuers'))
            ->with('i', ($request->input('page', 1) - 1) * $rescuers->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $rescuer = new Rescuer();

        return view('rescuer.create', compact('rescuer'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RescuerRequest $request): RedirectResponse
    {
        Rescuer::create($request->validated());

        return Redirect::route('rescuers.index')
            ->with('success', 'Rescuer created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $rescuer = Rescuer::find($id);

        return view('rescuer.show', compact('rescuer'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $rescuer = Rescuer::find($id);

        return view('rescuer.edit', compact('rescuer'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RescuerRequest $request, Rescuer $rescuer): RedirectResponse
    {
        $rescuer->update($request->validated());

        return Redirect::route('rescuers.index')
            ->with('success', 'Rescuer updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        Rescuer::find($id)->delete();

        return Redirect::route('rescuers.index')
            ->with('success', 'Rescuer deleted successfully');
    }
}
