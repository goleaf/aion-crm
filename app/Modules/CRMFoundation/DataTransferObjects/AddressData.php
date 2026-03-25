<?php

namespace App\Modules\CRMFoundation\DataTransferObjects;

final readonly class AddressData
{
    public function __construct(
        public string $lineOne,
        public ?string $lineTwo,
        public string $city,
        public ?string $state,
        public ?string $postalCode,
        public string $countryCode,
    ) {}

    public static function fromArray(array $address): self
    {
        return new self(
            lineOne: trim($address['line_one']),
            lineTwo: self::normalizeNullable($address['line_two'] ?? null),
            city: trim($address['city']),
            state: self::normalizeNullable($address['state'] ?? null),
            postalCode: self::normalizeNullable($address['postal_code'] ?? null),
            countryCode: mb_strtoupper(trim($address['country_code'])),
        );
    }

    public function toArray(): array
    {
        return [
            'line_one' => $this->lineOne,
            'line_two' => $this->lineTwo,
            'city' => $this->city,
            'state' => $this->state,
            'postal_code' => $this->postalCode,
            'country_code' => $this->countryCode,
        ];
    }

    private static function normalizeNullable(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $normalized = trim($value);

        return $normalized === '' ? null : $normalized;
    }
}
