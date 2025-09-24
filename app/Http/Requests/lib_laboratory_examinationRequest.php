<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class lib_laboratory_examinationRequest extends FormRequest
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
            'item_id' => 'required|string|max:9',
            'item_description' => 'required|string|max:255',
            'service_fee' => 'required|numeric',
            'amount'  => 'required|numeric',
        ];
    }
}
