<?php

declare(strict_types=1);

namespace App\Enums;

use App\Models\Debt;
use App\Models\Quick;

enum MovementableEnum: string
{
    case DEBT = Debt::class;
    case QUICK = Quick::class;

    /**
     * check if can delete when delete movement
     *
     * @return boolean
     */
    public function canDelete(): bool
    {
        return in_array($this->value, [
            Quick::class
        ]);
    }
}
