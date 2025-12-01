<?php

namespace App\Http\Controllers;

use App\Models\Center;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\CenterRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class CenterController extends Controller
{
    public function __construct()
    {
        // Solo administradores pueden gestionar centros
        $this->middleware('role:admin');
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $centers = Center::paginate();

        return view('center.index', compact('centers'))
            ->with('i', ($request->input('page', 1) - 1) * $centers->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $center = new Center();

        return view('center.create', compact('center'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CenterRequest $request): RedirectResponse
    {
        Center::create($request->validated());

        return Redirect::route('centers.index')
            ->with('success', 'Centro creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $center = Center::find($id);

        return view('center.show', compact('center'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $center = Center::find($id);

        return view('center.edit', compact('center'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CenterRequest $request, Center $center): RedirectResponse
    {
        $center->update($request->validated());

        return Redirect::route('centers.index')
            ->with('success', 'Centro actualizado correctamente');
    }

    public function destroy($id): RedirectResponse
    {
        Center::find($id)->delete();

        return Redirect::route('centers.index')
            ->with('success', 'Centro eliminado correctamente');
    }
}
