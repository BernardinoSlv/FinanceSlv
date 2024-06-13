<?php

namespace App\Http\Requests;

use App\Enums\MovementTypeEnum;
use App\Rules\Amount;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMovementRequest extends FormRequest
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
            "type" => [
                "required", Rule::in([
                    MovementTypeEnum::IN->value,
                    MovementTypeEnum::OUT->value,
                ])
            ],
            "amount" => ["required", new Amount],

        ];
    }
}
