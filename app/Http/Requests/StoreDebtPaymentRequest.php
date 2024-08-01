<?php

namespace App\Http\Requests;

use App\Enums\MovementTypeEnum;
use App\Models\Debt;
use App\Rules\Amount;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Src\Parsers\RealToFloatParser;

class StoreDebtPaymentRequest extends FormRequest
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
            "amount" => [
                "required",
                "string",
                new Amount,
                function (string $attributes, mixed $value, Closure $fail) {
                    /** @var Debt */
                    $debt = $this->route("debt");
                    $totalPaid = (float) $debt->movements()
                        ->where("movements.type", MovementTypeEnum::OUT->value)
                        ->sum("amount");
                    $amount = RealToFloatParser::parse($value);

                    if ($totalPaid + $amount > floatval($debt->amount)) {
                        $fail("O valor do pagamento excedeu o total da dívida.");
                    }
                }
            ]
        ];
    }
}
