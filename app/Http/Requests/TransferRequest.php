<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransferRequest extends FormRequest
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
            // Dos modos:
            // - Primer traslado: report_id requerido, persona/animal no requeridos
            // - Traslado interno: animal_id requerido, persona requerida (o la define el servidor)
            'report_id' => 'nullable|exists:reports,id|prohibited_with:animal_id,animal_file_id',
            'persona_id' => 'required_without:report_id|nullable|exists:people,id',
            'animal_id' => 'required_without:report_id|nullable|exists:animals,id|prohibited_with:report_id',
            'animal_file_id' => 'nullable|exists:animal_files,id|prohibited_with:report_id',
            'centro_id' => 'required|exists:centers,id',
            'observaciones' => 'nullable|string',
        ];
    }
}
