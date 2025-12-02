<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PersonResource extends JsonResource
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
            'usuario_id' => $this->usuario_id,
            'nombre' => $this->nombre,
            'ci' => $this->ci,
            'telefono' => $this->telefono,
            'foto_path' => $this->foto_path,
            'es_cuidador' => $this->es_cuidador,
            'cuidador_center_id' => $this->cuidador_center_id,
            'cuidador_aprobado' => $this->cuidador_aprobado,
            'cuidador_motivo_revision' => $this->cuidador_motivo_revision,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            'highest_role' => $this->highest_role,
            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'email' => $this->user->email,
                    'name' => $this->user->name,
                ];
            }),
            'cuidador_center' => $this->whenLoaded('cuidadorCenter', function () {
                return [
                    'id' => $this->cuidadorCenter->id,
                    'nombre' => $this->cuidadorCenter->nombre,
                ];
            }),
        ];
    }
}

