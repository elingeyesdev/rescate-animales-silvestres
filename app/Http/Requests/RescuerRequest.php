<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RescuerRequest extends FormRequest
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
			'persona_id' => 'required',
			'cv_documentado' => 'nullable|string',
			'cv' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png,webp|max:5120',
            'aprobado' => 'nullable|boolean',
            'motivo_revision' => 'nullable|string',
        ];
    }
}
