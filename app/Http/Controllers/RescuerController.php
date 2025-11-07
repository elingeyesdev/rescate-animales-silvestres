<?php

namespace App\Http\Controllers;

use App\Models\Rescuer;
use App\Models\Person;
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
        $rescuers = Rescuer::with('person')->paginate();

        return view('rescuer.index', compact('rescuers'))
            ->with('i', ($request->input('page', 1) - 1) * $rescuers->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $rescuer = new Rescuer();
        $people = Person::orderBy('nombre')->get(['id','nombre']);
        return view('rescuer.create', compact('rescuer','people'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RescuerRequest $request): RedirectResponse
    {
        $data = $request->validated();
        if ($request->hasFile('cv')) {
            $data['cv_documentado'] = $request->file('cv')->store('cv', 'public');
        }
        Rescuer::create($data);

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
        $people = Person::orderBy('nombre')->get(['id','nombre']);
        return view('rescuer.edit', compact('rescuer','people'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RescuerRequest $request, Rescuer $rescuer): RedirectResponse
    {
        $data = $request->validated();
        if ($request->hasFile('cv')) {
            $data['cv_documentado'] = $request->file('cv')->store('cv', 'public');
        }
        $rescuer->update($data);

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
