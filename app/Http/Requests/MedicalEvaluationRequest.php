<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MedicalEvaluationRequest extends FormRequest
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
			'tratamiento_id' => 'nullable|exists:treatment_types,id',
			'descripcion' => 'string',
            'fecha' => 'nullable|date',
			'veterinario_id' => 'required',
            'imagen' => 'nullable|file|image|mimes:jpeg,jpg,png,webp|max:5120|dimensions:min_width=64,min_height=64',
        ];
    }
}
