<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UltraSoundRequest extends FormRequest
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

            'body_parts' => 'required|string|max:255',
            'rate' =>  'required|numeric',
            'service_fee' =>  'required|numeric',
            'total_amount' =>  'required|numeric',
        ];
    }
}
