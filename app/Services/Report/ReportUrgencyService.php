<?php

namespace App\Services\Report;

use App\Models\AnimalCondition;
use App\Models\IncidentType;

class ReportUrgencyService
{
    /**
     * Calcula un nivel de urgencia 1..5 basado en:
     * - severidad de la condición (1..5)
     * - riesgo del tipo de incidente (0..2)
     * - tamaño (pequeño/mediano/grande)
     * - si puede moverse (false aumenta)
     */
    public function compute(array $data): int
    {
        $cond = null;
        if (!empty($data['condicion_inicial_id'])) {
            $cond = AnimalCondition::find($data['condicion_inicial_id']);
        }
        $sev = $cond?->severidad ?? 3;

        $inc = null;
        if (!empty($data['tipo_incidente_id'])) {
            $inc = IncidentType::find($data['tipo_incidente_id']);
        }
        $risk = $inc?->riesgo ?? 1; // 0..2

        $sizeAdj = 0;
        $tam = $data['tamano'] ?? null;
        if ($tam === 'grande') {
            $sizeAdj = 1;
        } elseif ($tam === 'mediano') {
            $sizeAdj = 0;
        } else {
            $sizeAdj = 0;
        }

        $moveAdj = isset($data['puede_moverse']) && !$data['puede_moverse'] ? 1 : 0;

        $score = $sev + $risk + $sizeAdj + $moveAdj;
        if ($score < 1) $score = 1;
        if ($score > 5) $score = 5;
        return (int)$score;
    }
}





