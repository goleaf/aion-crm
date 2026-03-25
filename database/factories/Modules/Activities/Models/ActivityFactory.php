<?php

namespace Database\Factories\Modules\Activities\Models;

use App\Modules\Activities\Enums\ActivityPriorityEnum;
use App\Modules\Activities\Enums\ActivityStatusEnum;
use App\Modules\Activities\Enums\ActivityTypeEnum;
use App\Modules\Activities\Models\Activity;
use App\Modules\CRM\Models\Lead;
use App\Modules\Shared\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

/**
 * @extends Factory<Activity>
 */
class ActivityFactory extends Factory
{
    /** @var class-string<Activity> */
    protected $model = Activity::class;

    public function definition(): array
    {
        $relatedLead = Lead::factory()->create();

        return [
            'type' => ActivityTypeEnum::Task,
            'subject' => fake()->sentence(3),
            'description' => fake()->sentence(),
            'status' => ActivityStatusEnum::Planned,
            'priority' => ActivityPriorityEnum::Normal,
            'due_date' => now()->addDay(),
            'duration_minutes' => 30,
            'outcome' => null,
            'related_to_type' => $relatedLead->getMorphClass(),
            'related_to_id' => $relatedLead->getKey(),
            'owner_id' => User::factory(),
            'reminder_at' => now()->addHour(),
            'reminded_at' => null,
        ];
    }

    public function forRelatedTo(Model $relatedRecord): self
    {
        return $this->state(
            fn (): array => [
                'related_to_type' => $relatedRecord->getMorphClass(),
                'related_to_id' => $relatedRecord->getKey(),
            ],
        );
    }

    public function dueForReminder(): self
    {
        return $this->state(
            fn (): array => [
                'status' => ActivityStatusEnum::Planned,
                'reminder_at' => now()->subMinute(),
                'reminded_at' => null,
            ],
        );
    }
}
