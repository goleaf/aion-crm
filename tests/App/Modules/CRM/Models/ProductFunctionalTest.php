<?php

namespace Tests\App\Modules\CRM\Models;

use App\Modules\CRM\Enums\BillingFrequencyEnum;
use App\Modules\CRM\Foundation\Enums\CurrencyCodeEnum;
use App\Modules\CRM\Models\Product;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use Tests\Support\TestCases\FunctionalTestCase;

#[CoversClass(Product::class)]
#[CoversClass(BillingFrequencyEnum::class)]
#[CoversClass(CurrencyCodeEnum::class)]
#[Group('crm')]
class ProductFunctionalTest extends FunctionalTestCase
{
    public function test_it_casts_catalog_fields_and_computes_margin(): void
    {
        // Arrange

        $product = Product::factory()->create([
            'name' => 'Growth Retainer',
            'sku' => 'RET-001',
            'unit_price' => '149.00',
            'cost_price' => '89.50',
            'currency' => CurrencyCodeEnum::EUR,
            'tax_rate' => '21.00',
            'active' => true,
            'recurring' => true,
            'billing_frequency' => BillingFrequencyEnum::Monthly,
        ]);

        // Act

        $product->refresh();

        // Assert

        $this->assertSame('149.00', $product->unit_price);
        $this->assertSame('89.50', $product->cost_price);
        $this->assertSame('59.50', $product->margin);
        $this->assertSame(CurrencyCodeEnum::EUR, $product->currency);
        $this->assertSame(BillingFrequencyEnum::Monthly, $product->billing_frequency);
        $this->assertTrue($product->active);
        $this->assertTrue($product->recurring);
    }
}
