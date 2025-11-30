<?php

namespace App\Http\Controllers;

use App\Models\CareType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\CareTypeRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class CareTypeController extends Controller
{
    public function __construct()
    {
        // Solo administradores pueden gestionar tipos de cuidado
        $this->middleware('role:admin');
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $careTypes = CareType::paginate();

        return view('care-type.index', compact('careTypes'))
            ->with('i', ($request->input('page', 1) - 1) * $careTypes->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $careType = new CareType();

        return view('care-type.create', compact('careType'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CareTypeRequest $request): RedirectResponse
    {
        CareType::create($request->validated());

        return Redirect::route('care-types.index')
            ->with('success', 'Tipo de cuidado creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $careType = CareType::find($id);

        return view('care-type.show', compact('careType'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $careType = CareType::find($id);

        return view('care-type.edit', compact('careType'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CareTypeRequest $request, CareType $careType): RedirectResponse
    {
        $careType->update($request->validated());

        return Redirect::route('care-types.index')
            ->with('success', 'Tipo de cuidado actualizado correctamente');
    }

    public function destroy($id): RedirectResponse
    {
        CareType::find($id)->delete();

        return Redirect::route('care-types.index')
            ->with('success', 'Tipo de cuidado eliminado correctamente');
    }
}
