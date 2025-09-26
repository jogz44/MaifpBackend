<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssistanceRequest extends FormRequest
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
            'patient_id' => 'required|exists:patient,id',
            'transaction_id' => 'required|exists:transaction,id',

            'consultation_amount' => 'nullable|numeric',
            'laboratory_total' => 'nullable|numeric',
            'medication_total' => 'nullable|numeric',
            'total_billing' => 'nullable|numeric',
            'discount' => 'nullable|numeric',
            'final_billing' => 'nullable|numeric',
            // 'status' => 'required|in:Complete',

            'assistances' => 'required|array|min:1',
            'assistances.*.fund_source' => 'required|string',
            'assistances.*.fund_amount' => 'nullable|numeric',


            'medication' => 'nullable|array',
            'medication.*.item_description' => 'nullable|string',
            'medication.*.quantity' => 'nullable|integer',
            'medication.*.unit' => 'nullable|string',
            'medication.*.amount' => 'nullable|numeric',
            'medication.*.total' => 'nullable|numeric',
            'medication.*.transaction_date' => 'nullable|date',

            'laboratories_details' => 'nullable|array',
            'laboratories_details.*.laboratory_type' => 'nullable|string',
            // 'laboratories_details.*.amount' => 'nullable|numeric',
            'laboratories_details.*.total_amount' => 'nullable|numeric',


        ];
    }

    public function messages():array {

        return [

            'patient_id.exists' => 'The patient_id does not exist.',
            'transaction_id.exists' => 'The transaction_id does not exist.',

        ];
    }
}
