<?php

namespace App\Modules\CRM\DataTransferObjects;

use App\Modules\CRM\Enums\ActivityRecurrenceEnum;
use App\Modules\CRM\Enums\ActivityStatusEnum;
use App\Modules\CRM\Enums\ActivityTypeEnum;
use App\Modules\Shared\Models\User;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;

final readonly class UpsertActivityData
{
    /**
     * @param  list<int>  $attendeeIds
     */
    public function __construct(
        public string $title,
        public ActivityTypeEnum $type,
        public CarbonInterface $startAt,
        public CarbonInterface $endAt,
        public bool $allDay,
        public ?string $location,
        public ?string $description,
        public User $organizer,
        public array $attendeeIds,
        public ?int $reminderMinutes,
        public ActivityRecurrenceEnum $recurrence,
        public ActivityStatusEnum $status,
    ) {}

    /**
     * @param array{
     *     title: string,
     *     type: string,
     *     startAt: string,
     *     endAt: string,
     *     allDay: bool,
     *     location: ?string,
     *     description: ?string,
     *     attendeeIds: array<int, mixed>,
     *     reminderMinutes: mixed,
     *     recurrence: string,
     *     status: string
     * } $attributes
     */
    public static function fromArray(array $attributes, User $organizer): self
    {
        $startAt = CarbonImmutable::parse($attributes['startAt']);
        $endAt = CarbonImmutable::parse($attributes['endAt']);

        if ($attributes['allDay']) {
            $startAt = $startAt->startOfDay();
            $endAt = $endAt->endOfDay();
        }

        return new self(
            title: trim($attributes['title']),
            type: ActivityTypeEnum::from($attributes['type']),
            startAt: $startAt,
            endAt: $endAt,
            allDay: $attributes['allDay'],
            location: self::nullableTrim($attributes['location']),
            description: self::nullableTrim($attributes['description']),
            organizer: $organizer,
            attendeeIds: array_values(array_map(intval(...), $attributes['attendeeIds'])),
            reminderMinutes: $attributes['reminderMinutes'] === null || $attributes['reminderMinutes'] === '' ? null : (int) $attributes['reminderMinutes'],
            recurrence: ActivityRecurrenceEnum::from($attributes['recurrence']),
            status: ActivityStatusEnum::from($attributes['status']),
        );
    }

    private static function nullableTrim(?string $value): ?string
    {
        $trimmed = $value === null ? null : trim($value);

        return $trimmed === '' ? null : $trimmed;
    }
}
