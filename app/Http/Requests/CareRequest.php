<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CareRequest extends FormRequest
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
			'hoja_animal_id' => 'required',
			'tipo_cuidado_id' => 'required',
			'descripcion' => 'string',
            // fecha se asigna automáticamente por el sistema
            'imagen' => ['nullable', 'file', 'image', 'mimes:jpeg,jpg,png', 'max:5120', 'dimensions:min_width=64,min_height=64', new \App\Rules\NotWebpImage()],
        ];
    }
}
