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
            'firstname' => 'sometimes|required|string|max:255',
            'lastname' => 'sometimes|required|string|max:255',
            'middlename' => 'sometimes|nullable|string|max:255',
            'ext' => 'sometimes|nullable|string|max:255',
            'birthdate' => 'sometimes|required|date',
            'contact_number' => 'sometimes|nullable|string|max:11',
            'age' => 'sometimes|integer',
            'gender' => 'sometimes|required|string|max:11',
            'is_not_tagum' => 'sometimes|boolean',
            'street' => 'sometimes|nullable|string|max:255',
            'purok'  => 'sometimes|nullable|string|max:255',
            'barangay' => 'sometimes|required|string|max:255',
            'city' => 'sometimes|nullable|string|max:255',
            'province' => 'sometimes|nullable|string|max:255',
            'category' => 'sometimes|required|in:Child,Adult,Senior',
            'is_pwd' => 'sometimes|boolean',
            'is_solo' => 'sometimes|boolean',
            'user_id' => 'sometimes|required|exists:users,id'
        ];
    }
}
