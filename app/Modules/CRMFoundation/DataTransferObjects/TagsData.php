<?php

namespace App\Modules\CRMFoundation\DataTransferObjects;

final readonly class TagsData
{
    private function __construct(
        public array $values,
    ) {}

    public static function fromArray(array $values, int $maxTags = 25, int $maxLength = 32): self
    {
        $normalized = [];

        foreach ($values as $value) {
            $tag = self::normalizeTag($value, $maxLength);

            if ($tag === '') {
                continue;
            }

            if (! in_array($tag, $normalized, true)) {
                $normalized[] = $tag;
            }

            if (count($normalized) >= $maxTags) {
                break;
            }
        }

        return new self($normalized);
    }

    public function toArray(): array
    {
        return $this->values;
    }

    public function contains(string $value, int $maxLength = 32): bool
    {
        return in_array(self::normalizeTag($value, $maxLength), $this->values, true);
    }

    private static function normalizeTag(string $value, int $maxLength): string
    {
        $normalized = mb_strtolower(trim($value));
        $normalized = (string) preg_replace('/[^a-z0-9]+/u', '-', $normalized);
        $normalized = trim($normalized, '-');

        if ($normalized === '') {
            return '';
        }

        return mb_substr($normalized, 0, $maxLength);
    }
}
