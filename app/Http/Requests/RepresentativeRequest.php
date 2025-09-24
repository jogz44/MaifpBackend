<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RepresentativeRequest extends FormRequest
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
            'rep_name' => 'sometimes|nullable|string|max:255',
            'rep_relationship' => 'sometimes|nullable|string|max:255',
            'rep_contact' => 'sometimes|nullable|string|max:255',
            'rep_barangay' => 'sometimes|nullable|string|max:255',
            'rep_address' => 'sometimes|nullable|string|max:255',
            'rep_purok' => 'sometimes|nullable|string|max:255',
            'rep_street' => 'sometimes|nullable|string|max:255',
            'rep_province' => 'sometimes|nullable|string|max:255',
            'rep_city' => 'sometimes|nullable|string|max:255',

        ];
    }
}
