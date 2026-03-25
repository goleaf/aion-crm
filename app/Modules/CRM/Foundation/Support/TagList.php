<?php

namespace App\Modules\CRM\Foundation\Support;

final class TagList
{
    public static function normalize(array $tags): array
    {
        $normalized = [];

        foreach ($tags as $tag) {
            $value = self::slugify($tag);
            if ($value === '') {
                continue;
            }
            if (in_array($value, $normalized, true)) {
                continue;
            }

            $normalized[] = $value;
        }

        return $normalized;
    }

    private static function slugify(string $value): string
    {
        $trimmed = strtolower(trim($value));

        $withoutSymbols = preg_replace('/[^a-z0-9\s-]/', '', $trimmed) ?? '';
        $collapsedSpaces = preg_replace('/\s+/', '-', $withoutSymbols) ?? '';

        return trim($collapsedSpaces, '-');
    }
}
