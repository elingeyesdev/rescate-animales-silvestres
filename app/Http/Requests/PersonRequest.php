<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PersonRequest extends FormRequest
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
        // Si viene la acción del modal de cuidador, no validar campos requeridos normales
        if ($this->has('action') && $this->filled('action') && $this->filled('cuidador_motivo_revision')) {
            return [
                'action' => 'required|in:approve,reject',
                'cuidador_motivo_revision' => 'required|string|min:3',
            ];
        }
        
        $personId = $this->route('person') ? $this->route('person')->id : null;
        $userId = $this->route('person') && $this->route('person')->user ? $this->route('person')->user->id : null;
        $isCreating = !$personId; // Si no hay personId, es creación
        
        $rules = [
			'nombre' => 'required|string',
			'ci' => 'required|string|unique:people,ci' . ($personId ? ',' . $personId : ''),
			'telefono' => 'nullable|string',
			'es_cuidador' => 'nullable|boolean',
			'cuidador_center_id' => 'nullable|exists:centers,id',
			'cuidador_aprobado' => 'nullable|boolean',
			'cuidador_motivo_revision' => 'nullable|string',
        ];
        
        // Si es cuidador, el centro y motivo son requeridos
        if ($this->boolean('es_cuidador')) {
            $rules['cuidador_center_id'] = 'required|exists:centers,id';
            $rules['cuidador_motivo_revision'] = 'required|string|min:10';
        }
        
        // En creación, email y password son requeridos
        if ($isCreating) {
            $rules['email'] = 'required|email|unique:users,email';
            $rules['password'] = 'required|string|min:8|confirmed';
            $rules['password_confirmation'] = 'required|string|min:8';
        } else {
            // En edición, email es opcional pero debe ser único si se proporciona
            $rules['email'] = 'nullable|email|unique:users,email,' . ($userId ?? 'NULL') . ',id';
        }
        
        // Si es_cuidador es true, el centro y el motivo son requeridos
        if ($this->boolean('es_cuidador')) {
            $rules['cuidador_center_id'] = 'required|exists:centers,id';
            $rules['cuidador_motivo_revision'] = 'required|string|min:10';
        }
        
        return $rules;
    }
}
