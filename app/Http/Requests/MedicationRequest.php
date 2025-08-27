<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MedicationRequest extends FormRequest
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
            'transaction_id' => 'required|exists:transaction,id',
            // 'patient_id' => 'required|exists:patient,id',
            // 'medication_id' => 'required|exists:medication,id',
            'item_description' => 'required|string|max:255',
            'quantity' => 'required|integer',
            'unit' => 'required|string|max:255',
            'user_id' => 'exists:users,id',
            // 'transaction' => 'date',
            'amount'=> 'required|numeric',
            // 'status' => 'in:Pending,Done'


        ];
    }
}
