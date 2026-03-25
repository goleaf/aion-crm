<?php

namespace App\Modules\CRMFoundation\Support;

use Illuminate\Support\Str;

final class CrmUuid
{
    public static function generate(): string
    {
        return (string) Str::uuid();
    }

    public static function isValid(string $value): bool
    {
        return Str::isUuid($value);
    }
}
