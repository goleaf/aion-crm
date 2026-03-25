<?php

namespace Tests\App\Modules\CRM\Foundation\ValueObjects;

use App\Modules\CRM\Foundation\Enums\CurrencyCodeEnum;
use App\Modules\CRM\Foundation\ValueObjects\Money;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use Tests\Support\TestCases\UnitTestCase;

#[CoversClass(CurrencyCodeEnum::class)]
#[CoversClass(Money::class)]
#[Group('crm')]
class MoneyUnitTest extends UnitTestCase
{
    public function test_it_keeps_money_in_minor_units_with_decimal_rendering(): void
    {
        // Arrange

        $money = Money::fromMinor(12345, CurrencyCodeEnum::USD);

        // Act

        $result = $money->toArray();

        // Assert

        $this->assertSame(12345, $money->amountInMinorUnits);
        $this->assertSame('123.45', $money->toDecimal());
        $this->assertSame(
            [
                'amount_minor' => 12345,
                'currency' => 'USD',
            ],
            $result,
        );
    }

    public function test_it_adds_money_with_the_same_currency(): void
    {
        // Arrange

        $left = Money::fromMinor(1500, CurrencyCodeEnum::EUR);
        $right = Money::fromMinor(250, CurrencyCodeEnum::EUR);

        // Act

        $sum = $left->add($right);

        // Assert

        $this->assertSame(1750, $sum->amountInMinorUnits);
        $this->assertSame(CurrencyCodeEnum::EUR, $sum->currency);
    }

    public function test_it_rejects_addition_of_different_currencies(): void
    {
        // Arrange

        $left = Money::fromMinor(1500, CurrencyCodeEnum::EUR);
        $right = Money::fromMinor(250, CurrencyCodeEnum::USD);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Money values must use the same currency.');

        // Act

        $left->add($right);

        // Assert

        $this->assertTrue(true);
    }
}
