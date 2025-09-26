<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GuaranteeLetterRequest extends FormRequest
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
            'patient_id' => 'required|exists:patient,id',
            'transaction_id' => 'required|exists:transaction,id',
            'consultation_amount' =>'numeric',
            'laboratory_total' => 'numeric',
            'medication_total' => 'required|numeric',
            'total_billing' => 'required|numeric',
            'discount' => 'required|numeric',
            'final_billing' => 'required|numeric',
            
            'medication' => 'required|array',
            'medication.*.item_description' => 'required|string',
            'medication.*.quantity' => 'required|integer',
            'medication.*.unit' => 'required|string',
            'medication.*.amount' => 'required|numeric',
            'medication.*.total' => 'required|numeric',
            'medication.*.transaction_date' => 'required|date',

            'laboratories_details' => 'required|array',
            'laboratories_details.*.laboratory_type' => 'required|string',
            'laboratories_details.*.amount' => 'required|numeric',
        ];
    }

    public function messages(): array
    {
        return [
            'patient_id.exists' => 'The selected patient does not exist.',
            'transaction_id.exists' => 'The selected transaction does not exist.',
        ];
    }
}
