<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BillingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
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
            'patient_id'=> 'required|exists:patient,id',
            'laboratory_id' => 'required|exists:laboratory,id',
            'laboratory_type' => 'required|string|max:255',
            'amount' => 'required|numeric',
            'status' => 'required|in:Unpaid,Paid',

        ];
    }
}
