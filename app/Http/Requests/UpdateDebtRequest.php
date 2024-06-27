<?php

namespace App\Http\Requests;

use App\Rules\Amount;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDebtRequest extends FormRequest
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
            "identifier_id" => [
                "required",
                Rule::exists("identifiers", "id")
                    ->where("user_id", auth()->id())
            ],
            "title" => ["required", "string", "between:2,256"],
            "description" => ["nullable"],
            "amount" => ["required", new Amount],
            "installments" => ["nullable", "integer", "min:1"],
            "due_date" => ["nullable", "date"],
        ];
    }
}
