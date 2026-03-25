<?php

namespace App\Modules\CRM\Foundation\Support;

use Illuminate\Support\Str;

final class UuidPolicy
{
    public static function generate(): string
    {
        return (string) Str::uuid7();
    }

    public static function isValid(string $value): bool
    {
        return Str::isUuid($value);
    }
}
