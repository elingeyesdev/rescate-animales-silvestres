<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdoptionRequest extends FormRequest
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
        $id = $this->route('adoption')?->id;
        return [
			'direccion' => 'nullable|string',
			'detalle' => 'nullable|string',
			'latitud' => 'nullable|numeric',
			'longitud' => 'nullable|numeric',
			'aprobada' => 'required|boolean',
			'adoptante_id' => 'required',
            'animal_file_id' => [
                'required',
                'exists:animal_files,id',
                Rule::unique('adoptions', 'animal_file_id')->ignore($id),
            ],
        ];
    }
}
