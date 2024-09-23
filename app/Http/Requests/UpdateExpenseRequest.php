<?php

namespace App\Http\Requests;

use App\Rules\Amount;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateExpenseRequest extends FormRequest
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
                Rule::exists('identifiers', 'id')->where('user_id', auth()->id()),
            ],
            'title' => [
                'required',
                Rule::unique('expenses', 'title')
                    ->where('identifier_id', $this->identifier_id)
                    ->ignore($this->expense->id),
            ],
            'description' => ['nullable'],
            'amount' => ['required', new Amount],
            'due_day' => ['required', 'integer', 'min:1', 'max:31'],
        ];
    }
}
