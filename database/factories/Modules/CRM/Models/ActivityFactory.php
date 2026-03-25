<?php

namespace Database\Factories\Modules\CRM\Models;

use App\Modules\CRM\Enums\ActivityRecurrenceEnum;
use App\Modules\CRM\Enums\ActivityStatusEnum;
use App\Modules\CRM\Enums\ActivityTypeEnum;
use App\Modules\CRM\Models\Activity;
use App\Modules\Shared\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Activity>
 */
class ActivityFactory extends Factory
{
    protected $model = Activity::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'type' => fake()->randomElement(ActivityTypeEnum::cases()),
            'start_at' => now()->addDay(),
            'end_at' => now()->addDay()->addHour(),
            'all_day' => false,
            'location' => fake()->city(),
            'description' => fake()->sentence(),
            'organizer_id' => User::factory(),
            'reminder_minutes' => 15,
            'recurrence' => ActivityRecurrenceEnum::None,
            'status' => ActivityStatusEnum::Scheduled,
        ];
    }
}
