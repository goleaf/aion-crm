<?php

namespace App\Modules\CRM\Models;

use App\Modules\CRM\Enums\ActivityRecurrenceEnum;
use App\Modules\CRM\Enums\ActivityStatusEnum;
use App\Modules\CRM\Enums\ActivityTypeEnum;
use App\Modules\CRM\Foundation\Support\UuidPolicy;
use App\Modules\Shared\Models\User;
use Carbon\CarbonImmutable;
use Database\Factories\Modules\CRM\Models\ActivityFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property string $id
 * @property string $title
 * @property ActivityTypeEnum $type
 * @property CarbonImmutable $start_at
 * @property CarbonImmutable $end_at
 * @property bool $all_day
 * @property string|null $location
 * @property string|null $description
 * @property int $organizer_id
 * @property int|null $reminder_minutes
 * @property ActivityRecurrenceEnum $recurrence
 * @property ActivityStatusEnum $status
 * @property User|null $organizer
 * @property-read Collection<int, User> $attendees
 *
 * @method static Builder<static>|Activity query()
 */
class Activity extends Model
{
    /** @use HasFactory<ActivityFactory> */
    use HasFactory;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $table = 'activities';

    /**
     * @var list<string>
     */
    protected $fillable = [
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
    ];

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'all_day' => false,
        'recurrence' => 'none',
        'status' => 'scheduled',
    ];

    protected static function newFactory(): ActivityFactory
    {
        return ActivityFactory::new();
    }

    protected static function booted(): void
    {
        static::creating(function (self $activity): void {
            if ($activity->getKey() !== null) {
                return;
            }

            $activity->{$activity->getKeyName()} = UuidPolicy::generate();
        });
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => ActivityTypeEnum::class,
            'start_at' => 'immutable_datetime',
            'end_at' => 'immutable_datetime',
            'all_day' => 'boolean',
            'reminder_minutes' => 'integer',
            'recurrence' => ActivityRecurrenceEnum::class,
            'status' => ActivityStatusEnum::class,
        ];
    }

    public function organizer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    public function attendees(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'activity_attendees');
    }

    public function reminderAt(): ?CarbonImmutable
    {
        if ($this->reminder_minutes === null) {
            return null;
        }

        return $this->start_at->subMinutes($this->reminder_minutes);
    }

    protected function scopeVisibleForUser(Builder $query, User $user): Builder
    {
        return $query->where(function (Builder $visibilityQuery) use ($user): void {
            $visibilityQuery
                ->where('organizer_id', $user->id)
                ->orWhereHas('attendees', fn (Builder $attendeesQuery): Builder => $attendeesQuery->whereKey($user->id));
        });
    }
}
