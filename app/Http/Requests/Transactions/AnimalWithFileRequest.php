<?php

namespace App\Http\Requests\Transactions;

use Illuminate\Foundation\Http\FormRequest;

class AnimalWithFileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validaciones combinadas para Animal y AnimalFile (sin animal_id).
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Animal
            'nombre' => 'nullable|string',
            'sexo' => 'required|string|in:Hembra,Macho,Desconocido',
            'descripcion' => 'nullable|string',
            'reporte_id' => 'required|exists:reports,id',
            'llegaron_cantidad' => 'nullable|integer|min:1',
            // AnimalFile (sin animal_id, se asigna en servicio)
            'tipo_id' => 'required|exists:animal_types,id',
            'especie_id' => 'required|exists:species,id',
            'imagen' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'raza_id' => 'nullable|exists:breeds,id',
            'estado_id' => 'required|exists:animal_statuses,id',
        ];
    }
}


