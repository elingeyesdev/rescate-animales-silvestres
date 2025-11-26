<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CareFeedingRequest extends FormRequest
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
			'care_id' => 'required',
			'feeding_type_id' => 'required',
			'feeding_frequency_id' => 'required',
			'feeding_portion_id' => 'required',
        ];
    }
}
