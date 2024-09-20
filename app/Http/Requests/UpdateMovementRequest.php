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
            'fees_amount' => ["required_without:status", new Amount],
            "status" => ["required_without:fees_amount", "in:0,1"]
        ];
    }
}
