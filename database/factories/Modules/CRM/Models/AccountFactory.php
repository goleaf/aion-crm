<?php

namespace Database\Factories\Modules\CRM\Models;

use App\Modules\CRM\Enums\AccountIndustryEnum;
use App\Modules\CRM\Enums\AccountTypeEnum;
use App\Modules\CRM\Models\Account;
use App\Modules\CRM\Models\CrmTeam;
use App\Modules\Shared\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Account>
 */
class AccountFactory extends Factory
{
    /** @var class-string<Account> */
    protected $model = Account::class;

    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'industry' => fake()->randomElement(AccountIndustryEnum::cases()),
            'type' => fake()->randomElement(AccountTypeEnum::cases()),
            'website' => fake()->url(),
            'phone' => fake()->phoneNumber(),
            'email' => fake()->companyEmail(),
            'billing_address' => [
                'line_1' => fake()->streetAddress(),
                'line_2' => null,
                'city' => fake()->city(),
                'state_or_province' => fake()->state(),
                'postal_code' => fake()->postcode(),
                'country_code' => 'LT',
            ],
            'shipping_address' => null,
            'annual_revenue_minor' => fake()->numberBetween(100_000_00, 5_000_000_00),
            'employee_count' => fake()->numberBetween(5, 250),
            'owner_id' => User::factory(),
            'team_id' => CrmTeam::factory(),
            'parent_account_id' => null,
            'tags' => ['priority'],
        ];
    }
}
