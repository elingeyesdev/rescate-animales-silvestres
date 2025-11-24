<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AnimalFileRequest extends FormRequest
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
            'animal_id' => 'required',
			'tipo_id' => 'required',
			'especie_id' => 'required',
			'imagen_url' => 'nullable|string',
			'imagen' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
			'raza_id' => 'nullable|exists:breeds,id',
			'estado_id' => 'required',
        ];
    }
}
