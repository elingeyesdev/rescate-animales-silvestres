<?php

namespace App\Http\Requests\Transactions;

use Illuminate\Foundation\Http\FormRequest;

class CareProcessRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validaciones para el proceso transaccional de cuidado.
     */
    public function rules(): array
    {
        return [
            'animal_file_id' => 'required|exists:animal_files,id',
            'tipo_cuidado_id' => 'required|exists:care_types,id',
            'descripcion' => 'nullable|string',
            'fecha' => 'nullable|date',
            'observaciones' => 'nullable|string',
            'imagen' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120', new \App\Rules\NotWebpImage()],
        ];
    }
}







