<?php

namespace Tests\App\Livewire\CRM\Products;

use App\Livewire\CRM\Products\ProductsIndexPage;
use App\Modules\CRM\Enums\BillingFrequencyEnum;
use App\Modules\CRM\Foundation\Enums\CurrencyCodeEnum;
use App\Modules\CRM\Models\CrmTeam;
use App\Modules\CRM\Models\CrmUserProfile;
use App\Modules\CRM\Models\Product;
use App\Modules\Shared\Models\User;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use Tests\Support\TestCases\FunctionalTestCase;

#[CoversClass(ProductsIndexPage::class)]
#[Group('crm')]
class ProductsIndexPageFunctionalTest extends FunctionalTestCase
{
    public function test_it_renders_catalog_products_and_filters_by_search_term(): void
    {
        // Arrange

        $team = CrmTeam::factory()->create();
        $admin = User::factory()->create();

        CrmUserProfile::factory()->for($admin)->for($team, 'primaryTeam')->admin()->create();

        Product::factory()->create([
            'name' => 'Growth Retainer',
            'sku' => 'RET-001',
        ]);

        Product::factory()->create([
            'name' => 'Discovery Workshop',
            'sku' => 'WRK-001',
        ]);

        // Act

        $component = Livewire::actingAs($admin)
            ->test(ProductsIndexPage::class)
            ->set('search', 'Growth');

        // Assert

        $component
            ->assertSee('Growth Retainer')
            ->assertViewHas('products', fn ($products): bool => $products->pluck('name')->values()->all() === ['Growth Retainer']);
    }

    public function test_it_creates_a_product_from_the_livewire_form(): void
    {
        // Arrange

        $team = CrmTeam::factory()->create();
        $admin = User::factory()->create();

        CrmUserProfile::factory()->for($admin)->for($team, 'primaryTeam')->admin()->create();

        // Act

        Livewire::actingAs($admin)
            ->test(ProductsIndexPage::class)
            ->set('form.name', 'Implementation Sprint')
            ->set('form.sku', 'SPR-100')
            ->set('form.description', 'Structured implementation package')
            ->set('form.unit_price', '1200.00')
            ->set('form.currency', CurrencyCodeEnum::USD->value)
            ->set('form.category', 'Services')
            ->set('form.tax_rate', '0.00')
            ->set('form.active', true)
            ->set('form.recurring', false)
            ->set('form.billing_frequency', BillingFrequencyEnum::OneTime->value)
            ->set('form.cost_price', '700.00')
            ->call('saveProduct');

        // Assert

        $this->assertDatabaseHas('crm_products', [
            'name' => 'Implementation Sprint',
            'sku' => 'SPR-100',
            'currency' => CurrencyCodeEnum::USD->value,
            'billing_frequency' => BillingFrequencyEnum::OneTime->value,
        ]);
    }

    public function test_it_updates_an_existing_product_from_the_livewire_form(): void
    {
        // Arrange

        $team = CrmTeam::factory()->create();
        $admin = User::factory()->create();

        CrmUserProfile::factory()->for($admin)->for($team, 'primaryTeam')->admin()->create();

        $product = Product::factory()->create([
            'name' => 'Growth Retainer',
            'sku' => 'RET-001',
            'recurring' => true,
            'billing_frequency' => BillingFrequencyEnum::Monthly,
        ]);

        // Act

        Livewire::actingAs($admin)
            ->test(ProductsIndexPage::class)
            ->call('editProduct', $product->getKey())
            ->set('form.name', 'Growth Retainer Plus')
            ->set('form.sku', 'RET-010')
            ->set('form.unit_price', '199.00')
            ->set('form.cost_price', '120.00')
            ->set('form.tax_rate', '9.00')
            ->call('saveProduct');

        // Assert

        $this->assertDatabaseHas('crm_products', [
            'id' => $product->getKey(),
            'name' => 'Growth Retainer Plus',
            'sku' => 'RET-010',
            'unit_price' => '199.00',
            'cost_price' => '120.00',
            'tax_rate' => '9.00',
        ]);
    }
}
