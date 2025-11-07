<?php

namespace App\Http\Controllers;

use App\Models\Care;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\CareRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class CareController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $cares = Care::paginate();

        return view('care.index', compact('cares'))
            ->with('i', ($request->input('page', 1) - 1) * $cares->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $care = new Care();

        return view('care.create', compact('care'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CareRequest $request): RedirectResponse
    {
        Care::create($request->validated());

        return Redirect::route('cares.index')
            ->with('success', 'Care created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $care = Care::find($id);

        return view('care.show', compact('care'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $care = Care::find($id);

        return view('care.edit', compact('care'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CareRequest $request, Care $care): RedirectResponse
    {
        $care->update($request->validated());

        return Redirect::route('cares.index')
            ->with('success', 'Care updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        Care::find($id)->delete();

        return Redirect::route('cares.index')
            ->with('success', 'Care deleted successfully');
    }
}
