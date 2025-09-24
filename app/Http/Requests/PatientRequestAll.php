<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PatientRequestAll extends FormRequest
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

    public function prepareForValidation(){

        $this->merge([
            'street' => $this->input('street') ?: 'NA', // ✅ if street is null/empty → NA
        ]);
    }

    public function rules(): array
    {


        return [
            // patient
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'middlename' => 'sometimes|nullable|string|max:255',
            'ext' => 'sometimes|nullable|string|max:255',
            'birthdate' => 'sometimes|required|date',
            'contact_number' => 'sometimes|nullable|string|max:11',
            'age' => 'sometimes|integer',
            'gender' => 'required|string|max:11',
            'is_not_tagum' => 'sometimes|boolean',
            'street' => 'sometimes|nullable|string|max:255',
            'purok'  => 'sometimes|nullable|string|max:255',
            'barangay' => 'sometimes|required|string|max:255',
            'city' => 'sometimes|nullable|string|max:255',
            'province' => 'sometimes|nullable|string|max:255',

            'permanent_street' => 'sometimes|nullable|string|max:255',
            'permanent_purok'  => 'sometimes|nullable|string|max:255',
            'permanent_barangay' => 'sometimes|required|string|max:255',
            'permanent_city' => 'sometimes|nullable|string|max:255',
            'permanent_province' => 'sometimes|nullable|string|max:255',

            'category' => 'sometimes|required|in:Child,Adult,Senior',
            'philsys_id' => 'sometimes|nullable|string|max:25',
            'philhealth_id'  => 'sometimes|nullable|string|max:25',
            'place_of_birth' => 'sometimes|nullable|string',
            'civil_status' => 'sometimes|nullable|string',
            'religion' => 'sometimes|nullable|string',
            'education' => 'sometimes|nullable|string',
            'occupation' => 'sometimes|nullable|string',
            'income' => 'sometimes|nullable|string',

            'is_pwd' => 'sometimes|boolean',
            'is_solo' => 'sometimes|boolean',
            'user_id' => 'sometimes|required|exists:users,id',

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


            //transaction
            'transaction_type' => 'required|string|max:255',
            'transaction_mode' => 'required|string|max:255',
            'transaction_date' => 'required|date',
            'purpose' => 'nullable|string|max:255',

            //vital
            'height' => 'sometimes|required|string|max:255',
            'weight' => 'required|string|max:255',
            'bmi' => 'sometimes|nullable|string|max:255',
            'temperature' => 'sometimes|nullable|string|max:255',
            'waist' => 'sometimes|nullable|string|max:255',
            'pulse_rate' => 'sometimes|nullable|string|max:255',
            'sp02' => 'sometimes|nullable|string|max:255',
            'heart_rate' => 'sometimes|nullable|string|max:255',
            'blood_pressure' => 'sometimes|nullable|string|max:255',
            'respiratory_rate' => 'sometimes|nullable|string|max:255',
            'medicine' => 'sometimes|nullable|string|max:255',
            'LMP' => 'sometimes|nullable|string|max:255',
        ];

    }
}
