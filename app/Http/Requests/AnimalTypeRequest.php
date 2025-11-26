<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AnimalTypeRequest extends FormRequest
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
            'permite_adopcion' => 'required|boolean',
            'permite_liberacion' => 'required|boolean',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'permite_adopcion' => filter_var($this->input('permite_adopcion', 0), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? 0,
            'permite_liberacion' => filter_var($this->input('permite_liberacion', 0), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? 0,
        ]);
    }

    public function withValidator($validator)
    {
        $validator->after(function ($v) {
			$adop = $this->boolean('permite_adopcion');
            $lib = $this->boolean('permite_liberacion');
            // Debe ser exactamente una en 1 y la otra en 0 (XOR)
            if ($adop === $lib) {
                $v->errors()->add('permite_liberacion', 'Debe seleccionar exactamente una opción: adopción o liberación, no ambas ni ninguna.');
            }
        });
    }
}
