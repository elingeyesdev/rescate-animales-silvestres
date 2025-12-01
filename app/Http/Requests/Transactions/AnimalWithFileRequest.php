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
            'modo_creacion' => 'nullable|in:uno_para_todos,uno_por_cada',
            'crear_cantidad' => 'nullable|required_if:modo_creacion,uno_por_cada|integer|min:1',
            'estado_inicial_id' => 'nullable|exists:animal_conditions,id',
            // AnimalFile (sin animal_id, se asigna en servicio)
            'especie_id' => 'required|exists:species,id',
            'imagen' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:4096', new \App\Rules\NotWebpImage()],
            'estado_id' => 'required|exists:animal_statuses,id',
        ];
    }
}


