<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BudgetAdditionalFundsRequest extends FormRequest
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
            'budget_id' => 'required|exists:budget,id',
            'additional' => 'required|numeric',
        ];
    }
    public function messages(): array
    {
        return [
            'budget_id.exists' => 'The selected budget_id does not exist.',
        ];
    }
}
