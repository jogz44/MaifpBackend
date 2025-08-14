<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PatientRequest extends FormRequest
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
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'middlename' => 'nullable|string|max:255',
            'ext' => 'nullable|string|max:255',
            'birthdate' => 'required|date',
            'contact_number' => 'nullable|string|max:11',
            'age' => 'integer',
            'gender' => 'required|string|max:11',
            'is_not_tagum' => 'boolean',
            'street' => 'nullable|string|max:255',
            'purok'  => 'nullable|string|max:255',
            'barangay' => 'required|string|max:255',
            'city' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:255',
            'category' => 'required|in:Child,Adult,Senior',
            'is_pwd' => 'boolean',
            'is_solo' => 'boolean',
            'user_id' => 'required|exists:users,id'
        ];
    }
}
