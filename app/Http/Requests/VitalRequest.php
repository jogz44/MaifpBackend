<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VitalRequest extends FormRequest
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
            //
            'height' => 'sometimes|required|string|max:255',
            'weight' => 'sometimes|required|string|max:255',
            'bmi' => 'sometimes|nullable|string|max:255',
            'temperature' => 'sometimes|nullable|string|max:255',
            'waist' => 'sometimes|nullable|string|max:255',
            'pulse_rate' => 'sometimes|nullable|string|max:255',
            'sp02' => 'sometimes|nullable|string|max:255',
            'heart_rate' => 'sometimes|nullable|string|max:255',
            'blood_pressure' => 'sometimes|nullable|string|max:255',
            'respiratory_rate' => 'sometimes|nullable|string|max:255',
            'medicine' => 'sometimes|nullable|string|max:255',
            'LMP' => 'sometimes|required|nullable|string|max:255',
        ];
    }
}
