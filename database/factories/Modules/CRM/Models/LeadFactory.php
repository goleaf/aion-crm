<?php

namespace Database\Factories\Modules\CRM\Models;

use App\Modules\CRM\Enums\LeadRatingEnum;
use App\Modules\CRM\Enums\LeadSourceEnum;
use App\Modules\CRM\Enums\LeadStatusEnum;
use App\Modules\CRM\Models\Lead;
use App\Modules\Shared\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Lead>
 */
class LeadFactory extends Factory
{
    /** @var class-string<Lead> */
    protected $model = Lead::class;

    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'company' => fake()->company(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->numerify('+3706#######'),
            'lead_source' => LeadSourceEnum::Referral,
            'status' => LeadStatusEnum::Qualified,
            'score' => 85,
            'rating' => LeadRatingEnum::Hot,
            'campaign_id' => null,
            'owner_id' => User::factory(),
            'converted' => false,
            'converted_to_contact_id' => null,
            'converted_to_deal_id' => null,
            'converted_at' => null,
            'description' => fake()->sentence(),
        ];
    }
}
