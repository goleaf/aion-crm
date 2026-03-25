<?php

namespace App\Modules\Activities\DataTransferObjects;

use App\Modules\Activities\Enums\ActivityPriorityEnum;
use App\Modules\Activities\Enums\ActivityStatusEnum;
use App\Modules\Activities\Enums\ActivityTypeEnum;
use App\Modules\CRM\Enums\CrmRecordTypeEnum;
use App\Modules\Shared\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;

final readonly class CreateActivityData
{
    /**
     * @param  array<int, User>  $attendees
     */
    public function __construct(
        public ActivityTypeEnum $type,
        public string $subject,
        public ?string $description,
        public ActivityStatusEnum $status,
        public ActivityPriorityEnum $priority,
        public ?CarbonImmutable $dueDate,
        public ?int $durationMinutes,
        public ?string $outcome,
        public ?Model $relatedTo,
        public User $owner,
        public array $attendees,
        public ?CarbonImmutable $reminderAt,
    ) {}

    /**
     * @param  array<int, int|string>  $attendeeIds
     */
    public static function fromInput(
        ActivityTypeEnum $type,
        string $subject,
        ?string $description,
        ActivityStatusEnum $status,
        ActivityPriorityEnum $priority,
        ?CarbonImmutable $dueDate,
        ?int $durationMinutes,
        ?string $outcome,
        ?CrmRecordTypeEnum $relatedToType,
        ?string $relatedToId,
        User $owner,
        array $attendeeIds,
        ?CarbonImmutable $reminderAt,
    ): self {
        $relatedTo = null;

        if ($relatedToType instanceof CrmRecordTypeEnum && $relatedToId !== null) {
            $relatedTo = $relatedToType
                ->modelClass()::query()
                ->findOrFail($relatedToId);
        }

        return new self(
            type: $type,
            subject: $subject,
            description: $description,
            status: $status,
            priority: $priority,
            dueDate: $dueDate,
            durationMinutes: $durationMinutes,
            outcome: $outcome,
            relatedTo: $relatedTo,
            owner: $owner,
            attendees: User::query()->whereKey($attendeeIds)->orderBy('name')->get()->all(),
            reminderAt: $reminderAt,
        );
    }
}
