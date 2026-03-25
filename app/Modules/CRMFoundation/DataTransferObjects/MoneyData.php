<?php

namespace App\Modules\CRMFoundation\DataTransferObjects;

use App\Modules\CRMFoundation\Enums\CurrencyCodeEnum;
use InvalidArgumentException;

final readonly class MoneyData
{
    public function __construct(
        public int $amountMinor,
        public CurrencyCodeEnum $currency,
    ) {}

    public static function fromDecimal(string $amountDecimal, CurrencyCodeEnum $currency, int $scale = 2): self
    {
        throw_if($scale < 0 || $scale > 6, InvalidArgumentException::class, 'Scale must be between 0 and 6.');

        $normalizedAmount = trim($amountDecimal);

        preg_match('/^(?<sign>-)?(?<whole>\d+)(?:\.(?<fraction>\d+))?$/', $normalizedAmount, $matches);

        throw_if($matches === [], InvalidArgumentException::class, 'Money amount must be a numeric decimal string.');

        $fraction = $matches['fraction'] ?? '';

        throw_if(mb_strlen($fraction) > $scale, InvalidArgumentException::class, 'Money amount has too many decimal places.');

        $multiplier = 10 ** $scale;
        $wholeMinor = ((int) $matches['whole']) * $multiplier;
        $fractionMinor = $fraction === '' ? 0 : (int) str_pad($fraction, $scale, '0');

        $amountMinor = $wholeMinor + $fractionMinor;

        if ($matches['sign'] === '-') {
            $amountMinor *= -1;
        }

        return new self($amountMinor, $currency);
    }

    public static function zero(CurrencyCodeEnum $currency): self
    {
        return new self(0, $currency);
    }

    public function toDecimal(int $scale = 2): string
    {
        throw_if($scale < 0 || $scale > 6, InvalidArgumentException::class, 'Scale must be between 0 and 6.');

        if ($scale === 0) {
            return (string) $this->amountMinor;
        }

        $multiplier = 10 ** $scale;
        $absoluteMinor = abs($this->amountMinor);
        $whole = intdiv($absoluteMinor, $multiplier);
        $fraction = $absoluteMinor % $multiplier;
        $sign = $this->amountMinor < 0 ? '-' : '';

        return $sign.sprintf('%d.%s', $whole, str_pad((string) $fraction, $scale, '0', STR_PAD_LEFT));
    }
}
