<?php

namespace App\Modules\CRM\Foundation\ValueObjects;

final readonly class PostalAddress
{
    private function __construct(
        public ?string $lineOne,
        public ?string $lineTwo,
        public ?string $city,
        public ?string $stateOrProvince,
        public ?string $postalCode,
        public string $countryCode,
    ) {}

    public static function fromArray(array $payload): self
    {
        return new self(
            lineOne: self::normalizeNullableString($payload['line_1'] ?? null),
            lineTwo: self::normalizeNullableString($payload['line_2'] ?? null),
            city: self::normalizeNullableString($payload['city'] ?? null),
            stateOrProvince: self::normalizeNullableString($payload['state_or_province'] ?? null),
            postalCode: self::normalizeNullableString($payload['postal_code'] ?? null),
            countryCode: self::normalizeCountryCode($payload['country_code'] ?? null),
        );
    }

    public function toArray(): array
    {
        return [
            'line_1' => $this->lineOne,
            'line_2' => $this->lineTwo,
            'city' => $this->city,
            'state_or_province' => $this->stateOrProvince,
            'postal_code' => $this->postalCode,
            'country_code' => $this->countryCode,
        ];
    }

    private static function normalizeNullableString(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $trimmed = trim($value);

        if ($trimmed === '') {
            return null;
        }

        return $trimmed;
    }

    private static function normalizeCountryCode(mixed $value): string
    {
        if (! is_string($value)) {
            return 'XX';
        }

        $trimmed = strtoupper(trim($value));

        if ($trimmed === '') {
            return 'XX';
        }

        return $trimmed;
    }
}
