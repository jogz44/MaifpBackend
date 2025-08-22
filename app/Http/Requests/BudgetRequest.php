<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BudgetRequest extends FormRequest
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
            'funds' => 'required|numeric',
            // 'budget_start_date' => 'required|date',
            // 'budget_end_date' => 'required|date',
            // 'remaining_funds' => 'numeric'
            'remarks' => 'required|string|max:255'

        ];
    }
}
