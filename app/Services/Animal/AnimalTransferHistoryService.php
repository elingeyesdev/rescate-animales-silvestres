<?php

namespace App\Services\Animal;

use App\Models\AnimalFile;
use App\Models\AnimalHistory;
use App\Models\Center;
use App\Models\Transfer;

class AnimalTransferHistoryService
{
    /**
     * Registra en historial el primer traslado originado por un hallazgo (sin hoja aún).
     */
    public function logFirstTransfer(Transfer $transfer, ?int $reporteId = null): void
    {
        AnimalHistory::create([
            'animal_file_id' => null,
            'valores_antiguos' => null,
            'valores_nuevos' => [
                'transfer' => [
                    'id' => $transfer->id,
                    'persona_id' => $transfer->persona_id,
                    'reporte_id' => $reporteId,
                    'centro_id' => $transfer->centro_id,
                    'observaciones' => $transfer->observaciones,
                    'primer_traslado' => true,
                    'latitud' => $transfer->latitud ?? null,
                    'longitud' => $transfer->longitud ?? null,
                ],
                // No hay hoja/centro anterior aún
            ],
            'observaciones' => [
                'texto' => 'Primer traslado desde reporte de hallazgo',
            ],
        ]);
    }

    /**
     * Registra en historial un traslado entre centros (con hoja de vida).
     * Incluye el centro anterior y el nuevo.
     */
    public function logInternalTransfer(Transfer $transfer): void
    {
        if (empty($transfer->animal_id)) {
            return;
        }

        $animalFile = AnimalFile::where('animal_id', $transfer->animal_id)
            ->orderByDesc('id')
            ->first();
        if (!$animalFile) {
            return;
        }

        // Centro anterior: último traslado anterior a este para el mismo animal
        $prev = Transfer::where('animal_id', $transfer->animal_id)
            ->where('id', '<', $transfer->id)
            ->orderByDesc('id')
            ->first();
        $oldCenter = $prev ? $prev->center : null;
        $newCenter = $transfer->center ?: Center::find($transfer->centro_id);

        $oldValues = null;
        if ($oldCenter) {
            $oldValues = [
                'centro' => [
                    'id' => $oldCenter->id,
                    'nombre' => $oldCenter->nombre,
                ],
            ];
        }

        $newValues = [
            'transfer' => [
                'id' => $transfer->id,
                'persona_id' => $transfer->persona_id,
                'centro_id' => $transfer->centro_id,
                'observaciones' => $transfer->observaciones,
                'primer_traslado' => false,
                'latitud' => $transfer->latitud ?? null,
                'longitud' => $transfer->longitud ?? null,
            ],
            'centro' => $newCenter ? [
                'id' => $newCenter->id,
                'nombre' => $newCenter->nombre,
            ] : null,
        ];

        AnimalHistory::create([
            'animal_file_id' => $animalFile->id,
            'valores_antiguos' => $oldValues,
            'valores_nuevos' => $newValues,
            'observaciones' => [
                'texto' => 'Registro de traslado entre centros',
            ],
        ]);
    }
}


