<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReportRequest extends FormRequest
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
        $rules = [
			// persona_id and aprobado are set server-side
			'cantidad_animales' => 'required|integer|min:1',
			'imagen' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
			'observaciones' => 'nullable|string',
			'latitud' => 'nullable|numeric',
			'longitud' => 'nullable|numeric',
			'direccion' => 'nullable|string',
            'traslado_inmediato' => 'nullable|boolean',
            'centro_id' => 'nullable|exists:centers,id|required_if:traslado_inmediato,1',
        ];

        if (in_array($this->method(), ['PUT','PATCH'])) {
            $rules['aprobado'] = 'required|boolean';
        }

        return $rules;
    }
}
