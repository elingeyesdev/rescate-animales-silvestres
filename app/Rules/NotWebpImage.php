<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class NotWebpImage implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value && $value->isValid()) {
            $extension = strtolower($value->getClientOriginalExtension());
            $mimeType = $value->getMimeType();
            
            if ($extension === 'webp' || $mimeType === 'image/webp') {
                $fail('El formato de imagen .webp no está permitido. Por favor, usa JPG, JPEG o PNG.');
            }
        }
    }
}
