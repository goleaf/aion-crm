<?php

namespace Database\Factories\Modules\CRM\Models;

use App\Modules\CRM\Enums\ContactLeadSourceEnum;
use App\Modules\CRM\Enums\ContactPreferredChannelEnum;
use App\Modules\CRM\Models\Account;
use App\Modules\CRM\Models\Contact;
use App\Modules\CRM\Models\CrmTeam;
use App\Modules\Shared\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Contact>
 */
class ContactFactory extends Factory
{
    /** @var class-string<Contact> */
    protected $model = Contact::class;

    public function definition(): array
    {
        return [
            'account_id' => Account::factory(),
            'owner_id' => User::factory(),
            'team_id' => CrmTeam::factory(),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'mobile' => fake()->phoneNumber(),
            'job_title' => fake()->jobTitle(),
            'department' => fake()->word(),
            'lead_source' => fake()->randomElement(ContactLeadSourceEnum::cases()),
            'do_not_contact' => false,
            'birthday' => fake()->date(),
            'preferred_channel' => fake()->randomElement(ContactPreferredChannelEnum::cases()),
            'notes' => fake()->sentence(),
        ];
    }
}
