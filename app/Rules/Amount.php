<?php

namespace App\Rules;

use App\Enums\RegexEnum;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class Amount implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! preg_match(RegexEnum::AMOUNT->value, $value)) {
            $fail('O campo valor não tem um formato válido');
        }
    }
}
