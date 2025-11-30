<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PersonRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
			'nombre' => 'required|string',
			'ci' => 'required|string',
			'telefono' => 'nullable|string',
			'es_cuidador' => 'nullable|boolean',
			'cuidador_center_id' => 'nullable|exists:centers,id',
			'cuidador_aprobado' => 'nullable|boolean',
			'cuidador_motivo_revision' => 'nullable|string',
        ];
    }
}
