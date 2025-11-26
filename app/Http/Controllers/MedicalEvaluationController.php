<?php

namespace App\Http\Controllers;

use App\Models\MedicalEvaluation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\MedicalEvaluationRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Models\TreatmentType;
use App\Models\Veterinarian;

class MedicalEvaluationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $medicalEvaluations = MedicalEvaluation::with(['treatmentType','veterinarian.person','animalFile.animal'])->paginate();

        return view('medical-evaluation.index', compact('medicalEvaluations'))
            ->with('i', ($request->input('page', 1) - 1) * $medicalEvaluations->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $medicalEvaluation = new MedicalEvaluation();
        $treatmentTypes = TreatmentType::orderBy('nombre')->get(['id','nombre']);
        $veterinarians = Veterinarian::with('person')->where('aprobado', true)->orderBy('id')->get();

        return view('medical-evaluation.create', compact('medicalEvaluation','treatmentTypes','veterinarians'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MedicalEvaluationRequest $request): RedirectResponse
    {
        $data = $request->validated();
        // Fecha por defecto: hoy según timezone de la app (UTC-4 configurado)
        if (empty($data['fecha'])) {
            $data['fecha'] = Carbon::now()->toDateString();
        }
        if ($request->hasFile('imagen')) {
            $path = $request->file('imagen')->store('evidencias/medical-evaluations', 'public');
            // Guardar ruta relativa; la vista resolverá URL pública
            $data['imagen_url'] = $path;
        }
        MedicalEvaluation::create($data);

        return Redirect::route('medical-evaluations.index')
            ->with('success', 'Evaluación médica creada correctamente.');
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
        $treatmentTypes = TreatmentType::orderBy('nombre')->get(['id','nombre']);
        $veterinarians = Veterinarian::with('person')->where('aprobado', true)->orderBy('id')->get();

        return view('medical-evaluation.edit', compact('medicalEvaluation','treatmentTypes','veterinarians'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MedicalEvaluationRequest $request, MedicalEvaluation $medicalEvaluation): RedirectResponse
    {
        $data = $request->validated();
        if ($request->hasFile('imagen')) {
            $path = $request->file('imagen')->store('evidencias/medical-evaluations', 'public');
            // Guardar ruta relativa; la vista resolverá URL pública
            $data['imagen_url'] = $path;
        }
        $medicalEvaluation->update($data);

        return Redirect::route('medical-evaluations.index')
            ->with('success', 'Evaluación médica actualizada correctamente');
    }

    public function destroy($id): RedirectResponse
    {
        MedicalEvaluation::find($id)->delete();

        return Redirect::route('medical-evaluations.index')
            ->with('success', 'Evaluación médica eliminada correctamente');
    }
}
