<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReleaseRequest extends FormRequest
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
			'direccion' => 'nullable|string',
			'detalle' => 'nullable|string',
			'latitud' => 'nullable|numeric',
			'longitud' => 'nullable|numeric',
        ];
        if (in_array($this->method(), ['PUT','PATCH'])) {
            $rules['aprobada'] = 'required|boolean';
        } else {
            $rules['aprobada'] = 'nullable|boolean';
        }
        return $rules;
    }
}
