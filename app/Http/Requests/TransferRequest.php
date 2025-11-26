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
            // - Primer traslado: report_id presente, sin animal_id / animal_file_id
            // - Traslado interno: animal_id (o animal_file_id) presente, sin report_id
            'report_id' => 'nullable|exists:reports,id|prohibits:animal_id,animal_file_id',
            'persona_id' => 'required_without:report_id|nullable|exists:people,id',
            'animal_id' => 'required_without:report_id|nullable|exists:animals,id|prohibits:report_id',
            'animal_file_id' => 'nullable|exists:animal_files,id|prohibits:report_id',
            'centro_id' => 'required|exists:centers,id',
            'observaciones' => 'nullable|string',
        ];
    }
}
