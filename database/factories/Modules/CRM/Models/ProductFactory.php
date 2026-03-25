<?php

namespace Database\Factories\Modules\CRM\Models;

use App\Modules\CRM\Enums\BillingFrequencyEnum;
use App\Modules\CRM\Foundation\Enums\CurrencyCodeEnum;
use App\Modules\CRM\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement([
                'Growth Retainer',
                'Implementation Sprint',
                'Discovery Workshop',
                'Support Plan',
            ]),
            'sku' => fake()->unique()->bothify('SKU-###'),
            'description' => fake()->sentence(),
            'unit_price' => fake()->randomFloat(2, 49, 5000),
            'currency' => fake()->randomElement(CurrencyCodeEnum::cases()),
            'category' => fake()->randomElement(['Services', 'Subscriptions', 'Consulting', 'Support']),
            'tax_rate' => fake()->randomElement(['0.00', '9.00', '21.00']),
            'active' => fake()->boolean(85),
            'recurring' => fake()->boolean(60),
            'billing_frequency' => fake()->randomElement(BillingFrequencyEnum::cases()),
            'cost_price' => fake()->randomFloat(2, 0, 2500),
        ];
    }
}
