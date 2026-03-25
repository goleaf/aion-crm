<?php

namespace App\Modules\CRM\Foundation\ValueObjects;

use App\Modules\CRM\Foundation\Enums\CurrencyCodeEnum;
use InvalidArgumentException;

final readonly class Money
{
    private function __construct(
        public int $amountInMinorUnits,
        public CurrencyCodeEnum $currency,
    ) {}

    public static function fromMinor(int $amountInMinorUnits, CurrencyCodeEnum $currency): self
    {
        return new self($amountInMinorUnits, $currency);
    }

    public function toDecimal(): string
    {
        return number_format($this->amountInMinorUnits / 100, 2, '.', '');
    }

    public function add(self $other): self
    {
        throw_if($other->currency !== $this->currency, InvalidArgumentException::class, 'Money values must use the same currency.');

        return new self(
            amountInMinorUnits: $this->amountInMinorUnits + $other->amountInMinorUnits,
            currency: $this->currency,
        );
    }

    public function toArray(): array
    {
        return [
            'amount_minor' => $this->amountInMinorUnits,
            'currency' => $this->currency->value,
        ];
    }
}
