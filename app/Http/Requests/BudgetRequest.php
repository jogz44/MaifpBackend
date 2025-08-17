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
            'budget_date' => 'required|date',
            'additional_funds' => 'required|numeric',
            'remaining_funds' => 'required|numeric',
            'release_funds' => 'required|numeric',


        ];
    }
}
