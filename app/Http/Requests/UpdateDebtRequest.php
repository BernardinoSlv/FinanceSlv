<?php

namespace App\Http\Requests;

use App\Enums\RegexEnum;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateDebtRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
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
            "title" => ["required", "min:1", "max:256",],
            "description" => ["nullable"],
            "amount" => ["required", "regex:" . RegexEnum::AMOUNT->value],
            "start_at" => ["required", "date"]
        ];
    }
}
