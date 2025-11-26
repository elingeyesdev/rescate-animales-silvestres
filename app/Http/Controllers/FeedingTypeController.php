<?php

namespace App\Http\Controllers;

use App\Models\FeedingType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\FeedingTypeRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class FeedingTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $feedingTypes = FeedingType::paginate();

        return view('feeding-type.index', compact('feedingTypes'))
            ->with('i', ($request->input('page', 1) - 1) * $feedingTypes->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $feedingType = new FeedingType();

        return view('feeding-type.create', compact('feedingType'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(FeedingTypeRequest $request): RedirectResponse
    {
        FeedingType::create($request->validated());

        return Redirect::route('feeding-types.index')
            ->with('success', 'Tipo de alimentación creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $feedingType = FeedingType::find($id);

        return view('feeding-type.show', compact('feedingType'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $feedingType = FeedingType::find($id);

        return view('feeding-type.edit', compact('feedingType'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(FeedingTypeRequest $request, FeedingType $feedingType): RedirectResponse
    {
        $feedingType->update($request->validated());

        return Redirect::route('feeding-types.index')
            ->with('success', 'Tipo de alimentación actualizado correctamente');
    }

    public function destroy($id): RedirectResponse
    {
        FeedingType::find($id)->delete();

        return Redirect::route('feeding-types.index')
            ->with('success', 'Tipo de alimentación eliminado correctamente');
    }
}
