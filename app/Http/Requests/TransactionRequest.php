<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransactionRequest extends FormRequest
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
            'transaction_type' => 'sometimes|required|string|max:255',
             'transaction_mode' => 'sometimes|required|string|max:255',
             'transaction_date' => 'sometimes|required|date',
             'purpose' => 'sometimes|nullable|string|max:255',
        ];
    }
}
