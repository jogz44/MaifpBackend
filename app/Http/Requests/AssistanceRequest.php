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
            // 'laboratory_total' => 'nullable|numeric',
            'medication_total' => 'nullable|numeric',
            'total_billing' => 'nullable|numeric',
            'discount' => 'nullable|numeric',
            'final_billing' => 'nullable|numeric',
            // 'status' => 'required|in:Complete',

            'radiology_total' => 'nullable|numeric',
            'examination_total' => 'nullable|numeric',
            'ultrasound_total' => 'nullable|numeric',
            'mammogram_total' => 'nullable|numeric',

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



        ];
    }

    public function messages():array {

        return [

            'patient_id.exists' => 'The patient_id does not exist.',
            'transaction_id.exists' => 'The transaction_id does not exist.',

        ];
    }
}
