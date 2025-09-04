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

            // array of laboratories
            'laboratories' => 'required|array|min:1',
            'laboratories.*.laboratory_type' => 'required|string|max:255',
            'laboratories.*.amount' => 'required|numeric',
            // 'laboratories.*.status' => 'nullable|in:Pending,Returned,Done',
        ];
    }

    public function messages(): array
    {
    return [
            'transaction_id.exists' => 'The selected budget_id does not exist.',

        ];
    }
}
