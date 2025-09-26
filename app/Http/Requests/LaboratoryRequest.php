<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LaboratoryRequest extends FormRequest
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
            'transaction_id' => 'required|exists:transaction,id',

            // laboratories
            'laboratories' => 'nullable|array|min:1',
            'laboratories.*.laboratory_type' => 'required_with:laboratories|string|max:255',
            'laboratories.*.amount' => 'required_with:laboratories|numeric',
            'laboratories.*.service_fee' => 'required_with:laboratories|numeric',
            'laboratories.*.total_amount' => 'required_with:laboratories|numeric',

            // radiologies
            'radiologies' => 'nullable|array|min:1',
            'radiologies.*.item_description' => 'required_with:radiologies|string|max:255',
            'radiologies.*.selling_price' => 'required_with:radiologies|numeric',
            'radiologies.*.service_fee' => 'required_with:radiologies|numeric',
            'radiologies.*.total_amount' => 'required_with:radiologies|numeric',

            // examinations
            'examination' => 'nullable|array|min:1',
            'examination.*.item_id' => 'required_with:examination|numeric',
            'examination.*.item_description' => 'required_with:examination|string|max:255',
            'examination.*.selling_price' => 'required_with:examination|numeric',
            'examination.*.service_fee' => 'required_with:examination|numeric',
            'examination.*.total_amount' => 'required_with:examination|numeric',
        ];
    }

    public function messages(): array
    {
    return [
            'transaction_id.exists' => 'The selected transaction_id does not exist.',

        ];
    }
}
