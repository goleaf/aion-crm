<?php

namespace App\Modules\CRMFoundation\Support;

final class CrmTestId
{
    public static function forElement(string $scope, string $element, ?string $prefix = null): string
    {
        return self::build([$scope, $element], $prefix);
    }

    public static function forRow(string $scope, string|int $recordIdentifier, ?string $prefix = null): string
    {
        return self::build([$scope, 'row', (string) $recordIdentifier], $prefix);
    }

    private static function build(array $segments, ?string $prefix): string
    {
        $resolvedPrefix = $prefix ?? self::configuredPrefix();
        $normalizedPrefix = self::normalize($resolvedPrefix);

        $normalizedSegments = array_values(array_filter(
            array_map(self::normalize(...), $segments),
            fn (string $segment): bool => $segment !== '',
        ));

        if ($normalizedSegments === []) {
            return $normalizedPrefix;
        }

        return implode('-', [$normalizedPrefix, ...$normalizedSegments]);
    }

    private static function configuredPrefix(): string
    {
        if (! function_exists('config')) {
            return 'crm';
        }

        return (string) config('crm-foundation.test_ids.prefix', 'crm');
    }

    private static function normalize(string $segment): string
    {
        $normalized = mb_strtolower(trim($segment));
        $normalized = (string) preg_replace('/[^a-z0-9]+/u', '-', $normalized);

        return trim($normalized, '-');
    }
}
