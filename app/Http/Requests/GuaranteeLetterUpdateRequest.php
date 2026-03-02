<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GuaranteeLetterUpdateRequest extends FormRequest
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
           // ✅ Validate inputs

            // 'gl_number'   => 'required|string|unique:assistances,gl_number,' . $transaction_id . ',transaction_id',
            'gl_lgu'   => 'nullable|string',
            'gl_cong'  => 'nullable|string',


            'transaction_id' => 'required|exists:transactions,id',
            'consultation_amount' => 'nullable|numeric',

            'medication_total' => 'nullable|numeric',
            'total_billing' => 'nullable|numeric',
            'discount' => 'nullable|numeric',
            'final_billing' => 'nullable|numeric',


            'radiology_total' => 'nullable|numeric',
            'examination_total' => '|nullable|numeric',
            'ultrasound_total' => 'nullable|numeric',
            'mammogram_total' => 'nullable|numeric',


            'medication' => 'nullable|array',
            'medication.*.item_description' => 'nullable|string',
            'medication.*.quantity' => 'nullable|integer',
            'medication.*.unit' => 'nullable|string',
            'medication.*.amount' => 'nullable|numeric',
            'medication.*.total' => 'nullable|numeric',
            'medication.*.transaction_date' => 'nullable|date',

            'ultrasound_details' => 'nullable|array',
            'ultrasound_details.*.body_parts' => 'nullable|string',
            'ultrasound_details.*.total_amount' => 'nullable|numeric',

            'mammogram_details' => 'nullable|array',
            'mammogram_details.*.procedure' => 'nullable|string',
            'mammogram_details.*.total_amount' => 'nullable|numeric',

            'radiology_details' => 'nullable|array',
            'radiology_details.*.item_description' => 'nullable|string',
            'radiology_details.*.total_amount' => 'nullable|numeric',

            'examination_details' => 'nullable|array',
            'examination_details.*.item_description' => 'nullable|string',
            'examination_details.*.total_amount' => 'nullable|numeric',

            // ✅ Only validate array of funds
            'funds' => 'required|array|min:1',
            'funds.*.fund_source' => 'required|string',
            'funds.*.fund_amount' => 'required|numeric',



        ];
    }
}
