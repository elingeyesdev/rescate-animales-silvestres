<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AnimalProfileRequest extends FormRequest
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
			'estado_salud' => 'string',
			'sexo' => 'string',
			'especie' => 'required|string',
			'raza' => 'string',
			'alimentacion' => 'string',
			'frecuencia' => 'string',
			'color' => 'string',
			'imagen' => 'string',
			'detalle' => 'string',
        ];
    }
}
