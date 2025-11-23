<?php

namespace App\Http\Requests\Transactions;

use Illuminate\Foundation\Http\FormRequest;

class MedicalEvaluationProcessRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validaciones para el proceso de evaluación médica transaccional.
     */
    public function rules(): array
    {
        return [
            'animal_file_id' => 'required|exists:animal_files,id',
            'tratamiento_id' => 'required|exists:treatment_types,id',
            'veterinario_id' => 'required|exists:veterinarians,id',
            'estado_id' => 'required|exists:animal_statuses,id',
            'descripcion' => 'nullable|string',
            'fecha' => 'nullable|date',
            'observaciones' => 'nullable|string',
            'imagen' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ];
    }
}


