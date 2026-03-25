<?php

namespace App\Modules\CRM\Models;

use App\Modules\CRM\Authorization\CrmRecordVisibility;
use App\Modules\CRM\Enums\AccountIndustryEnum;
use App\Modules\CRM\Enums\AccountTypeEnum;
use App\Modules\CRM\Models\Concerns\UsesCrmPrimaryUuid;
use App\Modules\Shared\Models\User;
use Database\Factories\Modules\CRM\Models\AccountFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    /** @use HasFactory<AccountFactory> */
    use HasFactory;

    use UsesCrmPrimaryUuid;

    protected $table = 'crm_accounts';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'industry',
        'type',
        'website',
        'phone',
        'email',
        'billing_address',
        'shipping_address',
        'annual_revenue_minor',
        'employee_count',
        'owner_id',
        'team_id',
        'parent_account_id',
        'tags',
    ];

    protected static function newFactory(): AccountFactory
    {
        return AccountFactory::new();
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'industry' => AccountIndustryEnum::class,
            'type' => AccountTypeEnum::class,
            'billing_address' => 'array',
            'shipping_address' => 'array',
            'annual_revenue_minor' => 'integer',
            'employee_count' => 'integer',
            'tags' => 'array',
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
                ->where('name', 'like', "%{$term}%")
                ->orWhere('email', 'like', "%{$term}%")
                ->orWhere('website', 'like', "%{$term}%");
        });
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
     * @return BelongsTo<self, $this>
     */
    public function parentAccount(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_account_id');
    }

    /**
     * @return HasMany<self, $this>
     */
    public function childAccounts(): HasMany
    {
        return $this->hasMany(self::class, 'parent_account_id');
    }

    /**
     * @return HasMany<Contact, $this>
     */
    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class);
    }

    /**
     * @return HasMany<Deal, $this>
     */
    public function deals(): HasMany
    {
        return $this->hasMany(Deal::class);
    }
}
