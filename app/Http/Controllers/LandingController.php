<?php

namespace App\Http\Controllers;

use App\Models\Release;
use App\Models\Report;
use Illuminate\View\View;

class LandingController extends Controller
{
    /**
     * Mostrar la página de landing
     */
    public function index(): View
    {
        // Obtener liberaciones recientes
        $recentReleases = Release::with(['animalFile.species', 'animalFile.animal'])
            ->orderBy('created_at', 'desc')
            ->take(12)
            ->get();

        // Obtener reportes aprobados con coordenadas para el mapa
        $approvedReports = Report::with(['condicionInicial', 'incidentType'])
            ->where('aprobado', 1)
            ->whereNotNull('latitud')
            ->whereNotNull('longitud')
            ->orderByDesc('id')
            ->get()
            ->map(function ($report) {
                return [
                    'id' => $report->id,
                    'latitud' => $report->latitud,
                    'longitud' => $report->longitud,
                    'urgencia' => $report->urgencia,
                    'direccion' => $report->direccion,
                    'condicion_inicial' => $report->condicionInicial ? [
                        'nombre' => $report->condicionInicial->nombre,
                    ] : null,
                    'incident_type' => $report->incidentType ? [
                        'nombre' => $report->incidentType->nombre,
                    ] : null,
                ];
            });

        return view('landing', compact('recentReleases', 'approvedReports'));
    }
}

