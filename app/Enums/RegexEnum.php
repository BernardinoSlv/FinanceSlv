<?php

declare(strict_types=1);

namespace App\Enums;

enum RegexEnum: string
{
    case AMOUNT = "/^(\d{1,3}\.)*\d{1,3},\d{1,2}$/";
}
