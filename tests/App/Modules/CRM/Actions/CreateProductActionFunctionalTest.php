<?php

namespace Tests\App\Modules\CRM\Actions;

use App\Modules\CRM\Actions\CreateProductAction;
use App\Modules\CRM\DataTransferObjects\CreateProductData;
use App\Modules\CRM\Enums\BillingFrequencyEnum;
use App\Modules\CRM\Foundation\Enums\CurrencyCodeEnum;
use App\Modules\CRM\Models\Product;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use Tests\Support\TestCases\FunctionalTestCase;

#[CoversClass(CreateProductAction::class)]
#[CoversClass(CreateProductData::class)]
#[CoversClass(Product::class)]
#[Group('crm')]
class CreateProductActionFunctionalTest extends FunctionalTestCase
{
    public function test_it_creates_a_product_with_normalized_catalog_fields(): void
    {
        // Arrange

        $data = new CreateProductData(
            name: '  CRM Retainer  ',
            sku: ' ret-001 ',
            description: '  Monthly managed CRM service.  ',
            unitPrice: '249.00',
            currency: CurrencyCodeEnum::EUR,
            category: ' Services ',
            taxRate: '21.00',
            active: true,
            recurring: true,
            billingFrequency: BillingFrequencyEnum::Monthly,
            costPrice: '99.00',
        );

        // Act

        $product = resolve(CreateProductAction::class)->execute($data);

        // Assert

        $this->assertDatabaseHas('crm_products', [
            'id' => $product->getKey(),
            'name' => 'CRM Retainer',
            'sku' => 'RET-001',
            'description' => 'Monthly managed CRM service.',
            'unit_price' => '249.00',
            'currency' => CurrencyCodeEnum::EUR->value,
            'category' => 'Services',
            'tax_rate' => '21.00',
            'active' => true,
            'recurring' => true,
            'billing_frequency' => BillingFrequencyEnum::Monthly->value,
            'cost_price' => '99.00',
        ]);
    }
}
