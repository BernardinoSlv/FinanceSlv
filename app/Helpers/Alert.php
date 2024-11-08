<?php

declare(strict_types=1);

namespace App\Helpers;

class Alert
{
    public static function flashSuccess(string $text): void
    {
        session()->flash("alert_type", "success");
        session()->flash("alert_text", $text);
    }

    public static function success(string $text): array
    {
        return [
            'alert_type' => 'success',
            'alert_text' => $text,
        ];
    }

    public static function danger(string $text): array
    {
        return [
            'alert_type' => 'danger',
            'alert_text' => $text,
        ];
    }
}
