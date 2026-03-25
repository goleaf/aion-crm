<?php

namespace App\Livewire\CRM\Activities;

use App\Modules\CRM\Actions\CreateActivityAction;
use App\Modules\CRM\Actions\UpdateActivityAction;
use App\Modules\CRM\DataTransferObjects\UpsertActivityData;
use App\Modules\CRM\Enums\ActivityRecurrenceEnum;
use App\Modules\CRM\Enums\ActivityStatusEnum;
use App\Modules\CRM\Enums\ActivityTypeEnum;
use App\Modules\CRM\Models\Activity;
use App\Modules\CRM\Services\ActivityOccurrenceProjector;
use App\Modules\Shared\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Component;

class ActivitiesIndexPage extends Component
{
    public string $title = '';

    public string $type = 'meeting';

    public string $startAt = '';

    public string $endAt = '';

    public bool $allDay = false;

    public ?string $location = null;

    public ?string $description = null;

    /**
     * @var list<int>
     */
    public array $attendeeIds = [];

    public ?int $reminderMinutes = 15;

    public string $recurrence = 'none';

    public string $status = 'scheduled';

    public ?string $editingActivityId = null;

    public string $reminderFilter = 'all';

    public function mount(): void
    {
        $this->resetForm();
    }

    #[Computed]
    public function agendaItems(): array
    {
        $activities = $this->visibleActivitiesQuery($this->authenticatedUser())->get();
        $occurrences = resolve(ActivityOccurrenceProjector::class)->projectForRange(
            $activities,
            CarbonImmutable::now()->subDay(),
            CarbonImmutable::now()->addDays(30),
        );

        return array_values(array_filter(
            array_map($this->decorateOccurrence(...), $occurrences),
            fn (array $occurrence): bool => $this->passesReminderFilter($occurrence['reminder_state']),
        ));
    }

    /**
     * @return Collection<int, User>
     */
    #[Computed]
    public function availableUsers(): Collection
    {
        return User::query()
            ->select(['id', 'name'])
            ->orderBy('name')
            ->get();
    }

    public function save(): void
    {
        $user = $this->authenticatedUser();
        $validated = $this->validate($this->rules());
        $data = UpsertActivityData::fromArray($validated, $user);

        if ($this->editingActivityId === null) {
            resolve(CreateActivityAction::class)->execute($data);
        } else {
            $activity = $this->visibleActivitiesQuery($user)
                ->whereKey($this->editingActivityId)
                ->firstOrFail();

            resolve(UpdateActivityAction::class)->execute($activity, $data);
        }

        $this->resetForm();
        unset($this->agendaItems);
    }

    public function edit(string $activityId): void
    {
        $activity = $this->visibleActivitiesQuery($this->authenticatedUser())
            ->whereKey($activityId)
            ->firstOrFail();

        $this->editingActivityId = $activity->getKey();
        $this->title = $activity->title;
        $this->type = $activity->type->value;
        $this->startAt = $activity->start_at->format('Y-m-d\TH:i');
        $this->endAt = $activity->end_at->format('Y-m-d\TH:i');
        $this->allDay = $activity->all_day;
        $this->location = $activity->location;
        $this->description = $activity->description;
        $this->attendeeIds = $activity->attendees->pluck('id')->map(fn (mixed $id): int => (int) $id)->all();
        $this->reminderMinutes = $activity->reminder_minutes;
        $this->recurrence = $activity->recurrence->value;
        $this->status = $activity->status->value;
    }

    public function markCompleted(string $activityId): void
    {
        $this->updateStatus($activityId, ActivityStatusEnum::Completed);
    }

    public function cancel(string $activityId): void
    {
        $this->updateStatus($activityId, ActivityStatusEnum::Cancelled);
    }

    public function render(): View
    {
        return view('livewire.c-r-m.activities.activities-index-page');
    }

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::enum(ActivityTypeEnum::class)],
            'startAt' => ['required', 'date'],
            'endAt' => ['required', 'date', 'after_or_equal:startAt'],
            'allDay' => ['boolean'],
            'location' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'attendeeIds' => ['array'],
            'attendeeIds.*' => ['integer', 'exists:users,id'],
            'reminderMinutes' => ['nullable', 'integer', 'min:0', 'max:10080'],
            'recurrence' => ['required', Rule::enum(ActivityRecurrenceEnum::class)],
            'status' => ['required', Rule::enum(ActivityStatusEnum::class)],
        ];
    }

    private function authenticatedUser(): User
    {
        /** @var User|null $user */
        $user = auth()->guard('web')->user();

        abort_unless($user instanceof User, 403);

        return $user;
    }

    /**
     * @return Builder<Activity>
     */
    private function visibleActivitiesQuery(User $user): Builder
    {
        return Activity::query()
            ->select([
                'id',
                'title',
                'type',
                'start_at',
                'end_at',
                'all_day',
                'location',
                'description',
                'organizer_id',
                'reminder_minutes',
                'recurrence',
                'status',
            ])
            ->with([
                'organizer:id,name',
                'attendees:id,name',
            ])
            ->visibleForUser($user)
            ->orderBy('start_at');
    }

    /**
     * @param  array{activity: Activity, occurs_at: CarbonImmutable, ends_at: CarbonImmutable}  $occurrence
     * @return array{
     *     activity: Activity,
     *     occurs_at: CarbonImmutable,
     *     ends_at: CarbonImmutable,
     *     reminder_state: string
     * }
     */
    private function decorateOccurrence(array $occurrence): array
    {
        $reminderState = 'none';
        $now = CarbonImmutable::now();
        $reminderAt = $occurrence['activity']->reminderAt();

        if ($reminderAt !== null && $reminderAt->lte($now) && $occurrence['activity']->status === ActivityStatusEnum::Scheduled) {
            $reminderState = $occurrence['occurs_at']->lt($now) ? 'overdue' : 'due';
        }

        return [
            'activity' => $occurrence['activity'],
            'occurs_at' => $occurrence['occurs_at'],
            'ends_at' => $occurrence['ends_at'],
            'reminder_state' => $reminderState,
        ];
    }

    private function passesReminderFilter(string $reminderState): bool
    {
        if ($this->reminderFilter !== 'due') {
            return true;
        }

        return in_array($reminderState, ['due', 'overdue'], true);
    }

    private function resetForm(): void
    {
        $this->editingActivityId = null;
        $this->title = '';
        $this->type = ActivityTypeEnum::Meeting->value;
        $this->startAt = now()->addDay()->format('Y-m-d\TH:i');
        $this->endAt = now()->addDay()->addHour()->format('Y-m-d\TH:i');
        $this->allDay = false;
        $this->location = null;
        $this->description = null;
        $this->attendeeIds = [];
        $this->reminderMinutes = 15;
        $this->recurrence = ActivityRecurrenceEnum::None->value;
        $this->status = ActivityStatusEnum::Scheduled->value;
    }

    private function updateStatus(string $activityId, ActivityStatusEnum $status): void
    {
        $activity = $this->visibleActivitiesQuery($this->authenticatedUser())
            ->whereKey($activityId)
            ->firstOrFail();

        $activity->forceFill([
            'status' => $status,
        ])->save();

        unset($this->agendaItems);
    }
}
