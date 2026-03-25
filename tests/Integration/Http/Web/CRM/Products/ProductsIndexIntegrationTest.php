<?php

namespace Tests\Integration\Http\Web\CRM\Products;

use App\Livewire\CRM\Products\ProductsIndexPage;
use App\Modules\CRM\Models\CrmTeam;
use App\Modules\CRM\Models\CrmUserProfile;
use App\Modules\CRM\Models\Product;
use App\Modules\Shared\Models\User;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use Tests\Support\TestCases\FunctionalTestCase;

#[CoversClass(ProductsIndexPage::class)]
#[Group('crm')]
class ProductsIndexIntegrationTest extends FunctionalTestCase
{
    public function test_it_requires_authentication(): void
    {
        // Arrange

        // Act

        $response = $this->get('/crm/products');

        // Assert

        $response->assertRedirect(route('login'));
    }

    public function test_it_displays_the_products_registry_page_for_authenticated_users(): void
    {
        // Arrange

        $team = CrmTeam::factory()->create();
        $user = User::factory()->create();

        CrmUserProfile::factory()->for($user)->for($team, 'primaryTeam')->admin()->create();

        Product::factory()->create([
            'name' => 'Growth Retainer',
            'sku' => 'RET-001',
        ]);

        // Act

        $response = $this->actingAs($user, 'web')->get('/crm/products');

        // Assert

        $response
            ->assertOk()
            ->assertSee('wire:name="c-r-m.products.products-index-page"', false)
            ->assertSee('Growth Retainer');
    }

    public function test_it_integrates(): void
    {
        // Arrange

        $team = CrmTeam::factory()->create();
        $user = User::factory()->create();

        CrmUserProfile::factory()->for($user)->for($team, 'primaryTeam')->admin()->create();

        // Act

        $response = $this->actingAs($user, 'web')->get('/crm/products');

        // Assert

        $response->assertOk();
    }
}
