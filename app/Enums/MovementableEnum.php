<?php

declare(strict_types=1);

namespace App\Enums;

use App\Models\Debt;
use App\Models\Expense;
use App\Models\Quick;

enum MovementableEnum: string
{
    case DEBT = Debt::class;
    case QUICK = Quick::class;
    case EXPENSE = Expense::class;

    /**
     * check if can delete when delete movement
     */
    public function canDelete(): bool
    {
        return in_array($this->value, [
            Quick::class,
        ]);
    }

    public function getLabel(): string
    {
        return match($this) {
            self::DEBT => "Dívida",
            self::QUICK => "Simples",
            self::EXPENSE => "Despesa"
        };
    }
}
