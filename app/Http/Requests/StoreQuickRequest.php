<?php

namespace App\Http\Requests;

use App\Enums\MovementTypeEnum;
use App\Rules\Amount;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Src\Parsers\RealToFloatParser;

class StoreQuickRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
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
                "nullable",
                Rule::exists("identifiers", "id")
                    ->where("user_id", auth()->id())
            ],
            "title" => ["required_without:identifier_id", "nullable",  "string", "between:2,256"],
            "description" => ["nullable"],
            "type" => [
                "required", Rule::in([
                    MovementTypeEnum::IN->value,
                    MovementTypeEnum::OUT->value,
                ])
            ],
            "amount" => ["required", new Amount]
        ];
    }
}
