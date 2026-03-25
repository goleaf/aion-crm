<?php

namespace Database\Factories\Modules\CRM\Models;

use App\Modules\CRM\Enums\DealSourceEnum;
use App\Modules\CRM\Enums\DealStageEnum;
use App\Modules\CRM\Enums\DealTypeEnum;
use App\Modules\CRM\Foundation\Enums\CurrencyCodeEnum;
use App\Modules\CRM\Models\Account;
use App\Modules\CRM\Models\Contact;
use App\Modules\CRM\Models\CrmTeam;
use App\Modules\CRM\Models\Deal;
use App\Modules\CRM\Models\Pipeline;
use App\Modules\Shared\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Deal>
 */
class DealFactory extends Factory
{
    /** @var class-string<Deal> */
    protected $model = Deal::class;

    public function definition(): array
    {
        return [
            'name' => fake()->bs(),
            'account_id' => Account::factory(),
            'contact_id' => Contact::factory(),
            'owner_id' => User::factory(),
            'team_id' => CrmTeam::factory(),
            'stage' => DealStageEnum::Prospecting,
            'amount_minor' => fake()->numberBetween(10_000, 500_000),
            'currency' => fake()->randomElement(CurrencyCodeEnum::cases()),
            'probability' => fake()->numberBetween(10, 90),
            'close_date' => fake()->dateTimeBetween('now', '+90 days'),
            'deal_type' => fake()->randomElement(DealTypeEnum::cases()),
            'pipeline_id' => Pipeline::factory(),
            'lost_reason' => null,
            'source' => fake()->randomElement(DealSourceEnum::cases()),
        ];
    }
}
