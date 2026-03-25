<?php

namespace App\Modules\Activities\Models;

use App\Modules\Activities\Enums\ActivityPriorityEnum;
use App\Modules\Activities\Enums\ActivityStatusEnum;
use App\Modules\Activities\Enums\ActivityTypeEnum;
use App\Modules\Shared\Models\User;
use Database\Factories\Modules\Activities\Models\ActivityFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Activity extends Model
{
    /** @use HasFactory<ActivityFactory> */
    use HasFactory, HasUuids;

    protected $table = 'activities';

    public $incrementing = false;

    protected $keyType = 'string';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'type',
        'subject',
        'description',
        'status',
        'priority',
        'due_date',
        'duration_minutes',
        'outcome',
        'related_to_type',
        'related_to_id',
        'owner_id',
        'reminder_at',
        'reminded_at',
    ];

    protected static function newFactory(): ActivityFactory
    {
        return ActivityFactory::new();
    }

    protected function casts(): array
    {
        return [
            'type' => ActivityTypeEnum::class,
            'status' => ActivityStatusEnum::class,
            'priority' => ActivityPriorityEnum::class,
            'due_date' => 'immutable_datetime',
            'duration_minutes' => 'integer',
            'reminder_at' => 'immutable_datetime',
            'reminded_at' => 'immutable_datetime',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * @return BelongsToMany<User, $this>
     */
    public function attendees(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'activity_attendees',
            'activity_id',
            'user_id',
        );
    }

    public function relatedTo(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @param  Builder<Activity>  $query
     * @return Builder<Activity>
     */
    protected function scopeDueForReminder(Builder $query): Builder
    {
        return $query
            ->where('status', ActivityStatusEnum::Planned)
            ->whereNotNull('reminder_at')
            ->where('reminder_at', '<=', now())
            ->whereNull('reminded_at');
    }

    public function shouldSendReminder(): bool
    {
        return $this->status === ActivityStatusEnum::Planned
            && $this->reminder_at !== null
            && $this->reminder_at->isPast()
            && $this->reminded_at === null;
    }
}
