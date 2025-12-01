<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'motivo' => 'required|in:tardanza_respuesta,problema_cuenta,contacto_directo',
            'mensaje' => 'required|string|min:10|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'motivo.required' => 'Debes seleccionar un motivo.',
            'motivo.in' => 'El motivo seleccionado no es válido.',
            'mensaje.required' => 'Debes escribir un mensaje.',
            'mensaje.min' => 'El mensaje debe tener al menos 10 caracteres.',
            'mensaje.max' => 'El mensaje no puede exceder 1000 caracteres.',
        ];
    }
}

