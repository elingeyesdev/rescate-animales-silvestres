<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RescuerResource extends JsonResource
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
            'persona_id' => $this->persona_id,
            'cv_documentado' => $this->cv_documentado,
            'motivo_postulacion' => $this->motivo_postulacion,
            'aprobado' => $this->aprobado,
            'motivo_revision' => $this->motivo_revision,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            'person' => $this->whenLoaded('person', function () {
                return [
                    'id' => $this->person->id,
                    'nombre' => $this->person->nombre,
                    'ci' => $this->person->ci,
                    'telefono' => $this->person->telefono,
                    'foto_path' => $this->person->foto_path,
                ];
            }),
        ];
    }
}

