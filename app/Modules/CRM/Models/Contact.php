<?php

namespace App\Modules\CRM\Models;

use App\Modules\CRM\Authorization\CrmRecordVisibility;
use App\Modules\CRM\Enums\ContactLeadSourceEnum;
use App\Modules\CRM\Enums\ContactPreferredChannelEnum;
use App\Modules\CRM\Models\Concerns\UsesCrmPrimaryUuid;
use App\Modules\Shared\Models\User;
use Database\Factories\Modules\CRM\Models\ContactFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contact extends Model
{
    /** @use HasFactory<ContactFactory> */
    use HasFactory;

    use UsesCrmPrimaryUuid;

    protected $table = 'crm_contacts';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'account_id',
        'owner_id',
        'team_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'mobile',
        'job_title',
        'department',
        'lead_source',
        'do_not_contact',
        'birthday',
        'preferred_channel',
        'notes',
    ];

    protected static function newFactory(): ContactFactory
    {
        return ContactFactory::new();
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'lead_source' => ContactLeadSourceEnum::class,
            'do_not_contact' => 'boolean',
            'birthday' => 'immutable_date',
            'preferred_channel' => ContactPreferredChannelEnum::class,
        ];
    }

    /**
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    protected function scopeVisibleTo(Builder $query, User $actor): Builder
    {
        return CrmRecordVisibility::applyToQuery($query, $actor);
    }

    /**
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    protected function scopeSearch(Builder $query, ?string $search): Builder
    {
        $term = trim((string) $search);

        if ($term === '') {
            return $query;
        }

        return $query->where(function (Builder $builder) use ($term): void {
            $builder
                ->where('first_name', 'like', "%{$term}%")
                ->orWhere('last_name', 'like', "%{$term}%")
                ->orWhere('email', 'like', "%{$term}%");
        });
    }

    /**
     * @return BelongsTo<Account, $this>
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * @return BelongsTo<CrmTeam, $this>
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(CrmTeam::class, 'team_id');
    }

    /**
     * @return HasMany<Deal, $this>
     */
    public function deals(): HasMany
    {
        return $this->hasMany(Deal::class);
    }

    protected function fullName(): Attribute
    {
        return Attribute::get(fn (): string => trim(implode(' ', array_filter([
            $this->first_name,
            $this->last_name,
        ]))));
    }
}
