<?php

namespace App\Modules\CRM\Models;

use App\Modules\Activities\Models\Activity;
use App\Modules\CRM\Enums\LeadRatingEnum;
use App\Modules\CRM\Enums\LeadSourceEnum;
use App\Modules\CRM\Enums\LeadStatusEnum;
use App\Modules\CRM\Foundation\Support\UuidPolicy;
use App\Modules\Shared\Models\User;
use Carbon\CarbonImmutable;
use Database\Factories\Modules\CRM\Models\LeadFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @property string $lead_id
 * @property string $first_name
 * @property string|null $last_name
 * @property string|null $company
 * @property string|null $email
 * @property string|null $phone
 * @property LeadSourceEnum $lead_source
 * @property LeadStatusEnum $status
 * @property int $score
 * @property LeadRatingEnum $rating
 * @property int|null $campaign_id
 * @property int $owner_id
 * @property bool $converted
 * @property string|null $converted_to_contact_id
 * @property string|null $converted_to_deal_id
 * @property CarbonImmutable|null $converted_at
 * @property string|null $description
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 */
class Lead extends Model
{
    /** @use HasFactory<LeadFactory> */
    use HasFactory;

    protected $table = 'leads';

    protected $primaryKey = 'lead_id';

    protected $keyType = 'string';

    public $incrementing = false;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'lead_id',
        'first_name',
        'last_name',
        'company',
        'email',
        'phone',
        'lead_source',
        'status',
        'score',
        'rating',
        'campaign_id',
        'owner_id',
        'converted',
        'converted_to_contact_id',
        'converted_to_deal_id',
        'converted_at',
        'description',
    ];

    protected static function newFactory(): LeadFactory
    {
        return LeadFactory::new();
    }

    protected static function booted(): void
    {
        static::creating(function (self $lead): void {
            $lead->lead_id ??= UuidPolicy::generate();
            $lead->converted ??= false;
        });
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'lead_source' => LeadSourceEnum::class,
            'status' => LeadStatusEnum::class,
            'rating' => LeadRatingEnum::class,
            'score' => 'integer',
            'campaign_id' => 'integer',
            'owner_id' => 'integer',
            'converted' => 'boolean',
            'converted_to_contact_id' => 'string',
            'converted_to_deal_id' => 'string',
            'converted_at' => 'immutable_datetime',
        ];
    }

    protected function getNameAttribute(): string
    {
        return trim(collect([$this->first_name, $this->last_name])->implode(' '));
    }

    /**
     * @return list<string>
     */
    public static function listColumns(): array
    {
        return [
            'lead_id',
            'first_name',
            'last_name',
            'company',
            'email',
            'phone',
            'lead_source',
            'status',
            'score',
            'rating',
            'campaign_id',
            'owner_id',
            'converted',
            'converted_to_contact_id',
            'converted_to_deal_id',
            'converted_at',
            'description',
            'created_at',
            'updated_at',
        ];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function convertedContact(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'converted_to_contact_id');
    }

    public function convertedDeal(): BelongsTo
    {
        return $this->belongsTo(Deal::class, 'converted_to_deal_id');
    }

    /**
     * @return MorphMany<Activity, $this>
     */
    public function activities(): MorphMany
    {
        return $this->morphMany(Activity::class, 'relatedTo');
    }

    protected function scopeListView(Builder $query): void
    {
        $query->select(self::listColumns())
            ->with('owner:id,name');
    }

    protected function scopeOwnedBy(Builder $query, int|string|null $ownerId): void
    {
        if (blank($ownerId) || $ownerId === 'all') {
            return;
        }

        $query->where('owner_id', (int) $ownerId);
    }

    protected function scopeFromSource(Builder $query, ?string $leadSource): void
    {
        if (blank($leadSource) || $leadSource === 'all') {
            return;
        }

        $query->where('lead_source', $leadSource);
    }

    protected function scopeWithStatus(Builder $query, ?string $status): void
    {
        if (blank($status) || $status === 'all') {
            return;
        }

        $query->where('status', $status);
    }

    protected function scopeWithRating(Builder $query, ?string $rating): void
    {
        if (blank($rating) || $rating === 'all') {
            return;
        }

        $query->where('rating', $rating);
    }

    protected function scopeConverted(Builder $query, bool|string|null $converted): void
    {
        if ($converted === null || $converted === 'all') {
            return;
        }

        if (is_string($converted)) {
            $query->where('converted', $converted === '1' || $converted === 'true');

            return;
        }

        $query->where('converted', $converted);
    }

    protected function scopeOpen(Builder $query): void
    {
        $query->where('converted', false);
    }

    protected function scopeSearchTerm(Builder $query, ?string $term): void
    {
        if (blank($term)) {
            return;
        }

        $query->where(function (Builder $builder) use ($term): void {
            $builder
                ->where('first_name', 'like', '%'.$term.'%')
                ->orWhere('last_name', 'like', '%'.$term.'%')
                ->orWhere('company', 'like', '%'.$term.'%')
                ->orWhere('email', 'like', '%'.$term.'%')
                ->orWhere('phone', 'like', '%'.$term.'%');
        });
    }
}
