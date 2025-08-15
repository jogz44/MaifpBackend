<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddTransactionRequest extends FormRequest
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
            'patient_id' => 'required|integer',

            // Transaction fields
            'transaction_type' => 'required|string|max:255',
            'transaction_date' => 'required|string|max:255',
            'transaction_mode' => 'required|string|max:255',
            'purpose' => 'required|string|max:255',

            // Vital signs fields
            'height' => 'required|string|max:255',
            'weight' => 'required|string|max:255',
            'bmi' => 'nullable|string|max:255',
            'temperature' => 'nullable|string|max:255',
            'waist' => 'nullable|string|max:255',
            'pulse_rate' => 'nullable|string|max:255',
            'sp02' => 'nullable|string|max:255',
            'heart_rate' => 'nullable|string|max:255',
            'blood_pressure' => 'nullable|string|max:255',
            'respiratory_rate' => 'nullable|string|max:255',
            'medicine' => 'nullable|string|max:255',
            'LMP' => 'nullable|string|max:255',
        ];
    }
}
