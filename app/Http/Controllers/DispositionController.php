<?php

namespace App\Http\Controllers;

use App\Models\Disposition;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\DispositionRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class DispositionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $dispositions = Disposition::paginate();

        return view('disposition.index', compact('dispositions'))
            ->with('i', ($request->input('page', 1) - 1) * $dispositions->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $disposition = new Disposition();

        return view('disposition.create', compact('disposition'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(DispositionRequest $request): RedirectResponse
    {
        Disposition::create($request->validated());

        return Redirect::route('dispositions.index')
            ->with('success', 'Disposition created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $disposition = Disposition::find($id);

        return view('disposition.show', compact('disposition'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $disposition = Disposition::find($id);

        return view('disposition.edit', compact('disposition'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(DispositionRequest $request, Disposition $disposition): RedirectResponse
    {
        $disposition->update($request->validated());

        return Redirect::route('dispositions.index')
            ->with('success', 'Disposition updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        Disposition::find($id)->delete();

        return Redirect::route('dispositions.index')
            ->with('success', 'Disposition deleted successfully');
    }
}
