<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AnimalResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'sexo' => $this->sexo,
            'descripcion' => $this->descripcion,
            'reporte_id' => $this->reporte_id,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            'report' => $this->whenLoaded('report', function () {
                return [
                    'id' => $this->report->id,
                    'observaciones' => $this->report->observaciones,
                    'latitud' => $this->report->latitud,
                    'longitud' => $this->report->longitud,
                    'direccion' => $this->report->direccion,
                    'created_at' => $this->report->created_at?->toISOString(),
                ];
            }),
            'animal_files' => $this->whenLoaded('animalFiles', function () {
                return $this->animalFiles->map(function ($animalFile) {
                    return [
                        'id' => $animalFile->id,
                        'especie_id' => $animalFile->especie_id,
                        'imagen_url' => $animalFile->imagen_url,
                        'estado_id' => $animalFile->estado_id,
                        'centro_id' => $animalFile->centro_id,
                        'created_at' => $animalFile->created_at?->toISOString(),
                    ];
                });
            }),
        ];
    }
}

