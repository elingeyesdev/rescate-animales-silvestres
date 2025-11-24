<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Person;
use App\Models\Center;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\ReportRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Services\Animal\AnimalTransferTransactionalService;
use App\Models\AnimalHistory;

class ReportController extends Controller
{
    public function __construct(
        private readonly AnimalTransferTransactionalService $transferService
    ) {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $reports = Report::with('person')->paginate();

        return view('report.index', compact('reports'))
            ->with('i', ($request->input('page', 1) - 1) * $reports->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $report = new Report();

        $centers = Center::orderBy('nombre')->get(['id','nombre','latitud','longitud']);

        return view('report.create', compact('report','centers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ReportRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // Attach current user's person id
        $personId = Person::where('usuario_id', Auth::id())->value('id');
        $data['persona_id'] = $personId;
        $data['aprobado'] = 0;

        if ($request->hasFile('imagen')) {
            $path = $request->file('imagen')->store('reports', 'public');
            $data['imagen_url'] = $path;
        }
        // Accept lat/long/address from form (already validated as nullable)

        $report = Report::create($data);

        // Registrar evento de reporte en el historial (sin hoja)
        $hist = new AnimalHistory();
        $hist->animal_file_id = null;
        $hist->valores_antiguos = null;
        $hist->valores_nuevos = [
            'report' => [
                'id' => $report->id,
                'persona_id' => $report->persona_id,
                'direccion' => $report->direccion,
                'latitud' => $report->latitud,
                'longitud' => $report->longitud,
            ],
        ];
        $hist->observaciones = ['texto' => $report->observaciones ?? 'Registro de reporte'];
        $hist->changed_at = $report->created_at;
        $hist->save();

        // Si se marcó traslado inmediato, registrar primer traslado (sin hoja)
        if ($request->boolean('traslado_inmediato')) {
            $tData = [
                'persona_id' => $report->persona_id,
                'centro_id' => $request->input('centro_id'),
                'observaciones' => $report->observaciones,
                'primer_traslado' => true,
                'animal_id' => null,
                'latitud' => $report->latitud,
                'longitud' => $report->longitud,
                'reporte_id' => $report->id,
            ];
            $this->transferService->create($tData);
        }

        return Redirect::route('reports.index')
            ->with('success', 'El hallazgo se registró correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $report = Report::find($id);

        return view('report.show', compact('report'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $report = Report::find($id);

        return view('report.edit', compact('report'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ReportRequest $request, Report $report): RedirectResponse
    {
        $data = $request->validated();
        if ($request->hasFile('imagen')) {
            $path = $request->file('imagen')->store('reports', 'public');
            $data['imagen_url'] = $path;
        }

        $report->update($data);

        return Redirect::route('reports.index')
            ->with('success', 'El hallazgo se actualizó correctamente');
    }

    public function destroy($id): RedirectResponse
    {
        Report::find($id)->delete();

        return Redirect::route('reports.index')
            ->with('success', 'El hallazgo se eliminó correctamente');
    }
}
