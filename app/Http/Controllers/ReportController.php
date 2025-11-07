<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Person;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\ReportRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ReportController extends Controller
{
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

        return view('report.create', compact('report'));
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

        Report::create($data);

        return Redirect::route('reports.index')
            ->with('success', 'Reporte creado correctamente.');
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
            ->with('success', 'Reporte actualizado correctamente');
    }

    public function destroy($id): RedirectResponse
    {
        Report::find($id)->delete();

        return Redirect::route('reports.index')
            ->with('success', 'Reporte eliminado correctamente');
    }
}
