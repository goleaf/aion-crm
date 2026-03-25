<?php

namespace App\Modules\CRM\Foundation\Support;

final class DataTestId
{
    private const string PREFIX = 'crm';

    public static function for(string $module, string $element, array $context = []): string
    {
        $parts = [
            self::PREFIX,
            self::normalize($module),
            self::normalize($element),
        ];

        foreach ($context as $piece) {
            $parts[] = self::normalize($piece);
        }

        return implode('-', array_filter($parts));
    }

    private static function normalize(string $value): string
    {
        $trimmed = strtolower(trim($value));

        $withoutSymbols = preg_replace('/[^a-z0-9\s-]/', '', $trimmed) ?? '';
        $collapsedSpaces = preg_replace('/\s+/', '-', $withoutSymbols) ?? '';

        return trim($collapsedSpaces, '-');
    }
}
