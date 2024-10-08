<?php

namespace App\Http\Requests;

use App\Rules\Amount;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDebtRequest extends FormRequest
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
            'identifier_id' => [
                'required',
                Rule::exists('identifiers', 'id')
                    ->where('user_id', auth()->id()),
            ],
            'title' => ['required_without:identifier_id', 'nullable', 'string', 'between:2,256'],
            'description' => ['nullable'],
            'amount' => ['required', new Amount],
            'installments' => [
                "required_with:due_date",
                "nullable",
                'integer',
                'min:2'
            ],
            'due_date' => [
                "required_with:installments",
                "nullable",
                'date',
                "after_or_equal:tomorrow"
            ],
            'to_balance' => ['nullable', 'in:0,1'],
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            "to_balance" => intval(boolval($this->to_balance))
        ]);
    }
}
