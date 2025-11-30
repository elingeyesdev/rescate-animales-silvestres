<?php

namespace App\Http\Controllers;

use App\Models\TreatmentType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\TreatmentTypeRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class TreatmentTypeController extends Controller
{
    public function __construct()
    {
        // Solo administradores pueden gestionar tipos de tratamiento
        $this->middleware('role:admin');
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $treatmentTypes = TreatmentType::paginate();

        return view('treatment-type.index', compact('treatmentTypes'))
            ->with('i', ($request->input('page', 1) - 1) * $treatmentTypes->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $treatmentType = new TreatmentType();

        return view('treatment-type.create', compact('treatmentType'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TreatmentTypeRequest $request): RedirectResponse
    {
        TreatmentType::create($request->validated());

        return Redirect::route('treatment-types.index')
            ->with('success', 'Tipo de tratamiento creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $treatmentType = TreatmentType::find($id);

        return view('treatment-type.show', compact('treatmentType'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $treatmentType = TreatmentType::find($id);

        return view('treatment-type.edit', compact('treatmentType'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TreatmentTypeRequest $request, TreatmentType $treatmentType): RedirectResponse
    {
        $treatmentType->update($request->validated());

        return Redirect::route('treatment-types.index')
            ->with('success', 'Tipo de tratamiento actualizado correctamente');
    }

    public function destroy($id): RedirectResponse
    {
        TreatmentType::find($id)->delete();

        return Redirect::route('treatment-types.index')
            ->with('success', 'Tipo de tratamiento eliminado correctamente');
    }
}
