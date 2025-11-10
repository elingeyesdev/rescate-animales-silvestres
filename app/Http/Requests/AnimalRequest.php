<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AnimalRequest extends FormRequest
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
			'nombre' => 'nullable|string',
			'sexo' => 'required|string|in:Hembra,Macho,Desconocido',
			'descripcion' => 'nullable|string',
			'reporte_id' => 'required|exists:reports,id',
        ];
    }
}
