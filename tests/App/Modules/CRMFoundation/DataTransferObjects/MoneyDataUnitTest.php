<?php

namespace Tests\App\Modules\CRMFoundation\DataTransferObjects;

use App\Modules\CRMFoundation\DataTransferObjects\MoneyData;
use App\Modules\CRMFoundation\Enums\CurrencyCodeEnum;
use Generator;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use Tests\Support\TestCases\UnitTestCase;

#[CoversClass(MoneyData::class)]
#[CoversClass(CurrencyCodeEnum::class)]
#[Group('crm')]
class MoneyDataUnitTest extends UnitTestCase
{
    #[DataProvider('decimalToMinorDataProvider')]
    public function test_it_creates_minor_unit_money_from_decimal_strings(string $decimalAmount, int $expectedMinor): void
    {
        // Arrange

        // Act

        $money = MoneyData::fromDecimal($decimalAmount, CurrencyCodeEnum::Usd);

        // Assert

        $this->assertSame($expectedMinor, $money->amountMinor);
        $this->assertSame($decimalAmount, $money->toDecimal());
    }

    public function test_it_creates_zero_money_value(): void
    {
        // Arrange

        // Act

        $money = MoneyData::zero(CurrencyCodeEnum::Eur);

        // Assert

        $this->assertSame(0, $money->amountMinor);
        $this->assertSame(CurrencyCodeEnum::Eur, $money->currency);
    }

    public function test_it_rejects_money_with_too_many_decimals(): void
    {
        // Arrange

        $amount = '10.123';

        // Anticipate

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Money amount has too many decimal places.');

        // Act

        MoneyData::fromDecimal($amount, CurrencyCodeEnum::Usd);

        // Assert

        $this->assertTrue(true);
    }

    public static function decimalToMinorDataProvider(): Generator
    {
        yield 'positive amount' => [
            'decimalAmount' => '199.99',
            'expectedMinor' => 19999,
        ];

        yield 'whole amount' => [
            'decimalAmount' => '50.00',
            'expectedMinor' => 5000,
        ];

        yield 'negative amount' => [
            'decimalAmount' => '-7.50',
            'expectedMinor' => -750,
        ];
    }
}
