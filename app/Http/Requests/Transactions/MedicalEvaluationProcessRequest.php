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
            'tratamiento_id' => 'nullable|exists:treatment_types,id',
            'tratamiento_texto' => 'nullable|string',
            'veterinario_id' => 'required|exists:veterinarians,id',
            'estado_id' => 'nullable|exists:animal_statuses,id',
            'descripcion' => 'nullable|string',
            'diagnostico' => 'nullable|string',
            'peso' => 'nullable|numeric|min:0|max:100000',
            'temperatura' => 'nullable|numeric|min:0|max:100',
            'recomendacion' => 'nullable|string|in:traslado,observacion_24h,nueva_revision,tratamiento_prolongado',
            'apto_traslado' => 'required|string|in:si,no,con_restricciones',
            'fecha' => 'nullable|date',
            'observaciones' => 'nullable|string',
            'imagen' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ];
    }
}


