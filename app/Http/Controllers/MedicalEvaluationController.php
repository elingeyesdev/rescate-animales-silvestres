<?php

namespace App\Http\Controllers;

use App\Models\MedicalEvaluation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\MedicalEvaluationRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class MedicalEvaluationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $medicalEvaluations = MedicalEvaluation::paginate();

        return view('medical-evaluation.index', compact('medicalEvaluations'))
            ->with('i', ($request->input('page', 1) - 1) * $medicalEvaluations->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $medicalEvaluation = new MedicalEvaluation();

        return view('medical-evaluation.create', compact('medicalEvaluation'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MedicalEvaluationRequest $request): RedirectResponse
    {
        MedicalEvaluation::create($request->validated());

        return Redirect::route('medical-evaluations.index')
            ->with('success', 'MedicalEvaluation created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $medicalEvaluation = MedicalEvaluation::find($id);

        return view('medical-evaluation.show', compact('medicalEvaluation'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $medicalEvaluation = MedicalEvaluation::find($id);

        return view('medical-evaluation.edit', compact('medicalEvaluation'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MedicalEvaluationRequest $request, MedicalEvaluation $medicalEvaluation): RedirectResponse
    {
        $medicalEvaluation->update($request->validated());

        return Redirect::route('medical-evaluations.index')
            ->with('success', 'MedicalEvaluation updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        MedicalEvaluation::find($id)->delete();

        return Redirect::route('medical-evaluations.index')
            ->with('success', 'MedicalEvaluation deleted successfully');
    }
}
