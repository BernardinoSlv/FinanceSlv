<?php

namespace App\Http\Requests;

use App\Enums\RegexEnum;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StoreExpenseRequest extends FormRequest
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
            "title" => [
                "required",
                "min:1",
                "max:256",
                Rule::unique("expenses")
                    ->where(function (Builder $query): void {
                        $query->where("user_id", auth()->user()->id);
                    })
            ],
            "amount" => ["required", "regex:" . RegexEnum::AMOUNT->value],
            "quantity" => ["nullable", "numeric"],
            "description" => ["nullable"],
            "effetive_at" => ["nullable", "date"],
        ];
    }
}
