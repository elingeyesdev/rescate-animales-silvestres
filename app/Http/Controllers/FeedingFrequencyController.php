<?php

namespace App\Http\Controllers;

use App\Models\FeedingFrequency;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\FeedingFrequencyRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class FeedingFrequencyController extends Controller
{
    public function __construct()
    {
        // Solo administradores pueden gestionar frecuencias de alimentación
        $this->middleware('role:admin');
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $feedingFrequencies = FeedingFrequency::paginate();

        return view('feeding-frequency.index', compact('feedingFrequencies'))
            ->with('i', ($request->input('page', 1) - 1) * $feedingFrequencies->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $feedingFrequency = new FeedingFrequency();

        return view('feeding-frequency.create', compact('feedingFrequency'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(FeedingFrequencyRequest $request): RedirectResponse
    {
        FeedingFrequency::create($request->validated());

        return Redirect::route('feeding-frequencies.index')
            ->with('success', 'Frecuencia de alimentación creada correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $feedingFrequency = FeedingFrequency::find($id);

        return view('feeding-frequency.show', compact('feedingFrequency'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $feedingFrequency = FeedingFrequency::find($id);

        return view('feeding-frequency.edit', compact('feedingFrequency'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(FeedingFrequencyRequest $request, FeedingFrequency $feedingFrequency): RedirectResponse
    {
        $feedingFrequency->update($request->validated());

        return Redirect::route('feeding-frequencies.index')
            ->with('success', 'Frecuencia de alimentación actualizada correctamente');
    }

    public function destroy($id): RedirectResponse
    {
        FeedingFrequency::find($id)->delete();

        return Redirect::route('feeding-frequencies.index')
            ->with('success', 'Frecuencia de alimentación eliminada correctamente');
    }
}
