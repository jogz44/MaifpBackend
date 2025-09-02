<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NewConsultationRequest extends FormRequest
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
        'transaction_id'    => 'required|exists:transaction,id',
        'consultation_time'=>'nullable',
        'consultation_date' => 'required|date',
        'status' => 'required|in:Done,Processing,Pending,Returned,Medication',
        // 'amount'=> 'required'

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
