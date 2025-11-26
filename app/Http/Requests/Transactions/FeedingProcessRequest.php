<?php

namespace App\Http\Requests\Transactions;

use Illuminate\Foundation\Http\FormRequest;

class FeedingProcessRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validaciones para el proceso transaccional de alimentación.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'animal_file_id' => 'required|exists:animal_files,id',
            'feeding_type_id' => 'required|exists:feeding_types,id',
            'feeding_frequency_id' => 'required|exists:feeding_frequencies,id',
            'feeding_portion_id' => 'required|exists:feeding_portions,id',
            'descripcion' => 'nullable|string',
			// fecha y observaciones se gestionan automáticamente en el servidor
        ];
    }
}


