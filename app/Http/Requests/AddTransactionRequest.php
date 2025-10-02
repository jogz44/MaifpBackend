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

    public function prepareForValidation()
    {
        // All fields that should default to "NA" if empty
        $defaults = [
          
            // Representative
            'rep_name',
            'rep_relationship',
            'rep_contact',
            'rep_barangay',
            'rep_address',
            'rep_purok',
            'rep_street',
            'rep_province',
            'rep_city',

            // Transaction
            'transaction_type',
            'transaction_mode',
            'transaction_date',
            'purpose',

            // Vital
            'height',
            'weight',
            'bmi',
            'temperature',
            'waist',
            'pulse_rate',
            'sp02',
            'heart_rate',
            'blood_pressure',
            'respiratory_rate',
            'medicine',
            'LMP'
        ];

        $data = [];

        foreach ($defaults as $field) {
            $data[$field] = $this->input($field) ?: 'NA';
        }

        $this->merge($data);
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
            // representative
            'rep_name' => 'nullable|string|max:255',
            'rep_relationship' => 'nullable|string|max:255',
            'rep_contact' => 'nullable|string|max:255',
            'rep_barangay' => 'nullable|string|max:255',
            'rep_address' => 'nullable|string|max:255',
            'rep_purok' => 'nullable|string|max:255',
            'rep_street' => 'nullable|string|max:255',
            'rep_province' => 'nullable|string|max:255',
            'rep_city' => 'nullable|string|max:255',

            // Transaction fields
            'transaction_type' => 'required|string|max:255',
            'transaction_date' => 'required|string|max:255',
            'transaction_mode' => 'required|string|max:255',
            'purpose' => 'nullable|string|max:255',

            // Vital signs fields
            'height' => 'nullable|string|max:255',
            'weight' => 'nullable|string|max:255',
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
