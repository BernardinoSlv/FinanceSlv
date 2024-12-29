<?php

declare(strict_types=1);

namespace App\Support;

class Message
{
    public static function flashSuccess(string $text): void
    {
        session()->flash("message_type", "success");
        session()->flash("message_text", $text);
    }

    public static function success(string $text): array
    {
        return self::_make("success", $text);
    }

    public static function primary(string $text): array
    {
        return self::_make("primary", $text);
    }

    public static function danger(string $text): array
    {
        return self::_make("danger", $text);
    }


    protected static function _make(string $type, string $text): array
    {
        return [
            "message_type" => $type,
            "message_text" => $text
        ];
    }
}
