<?php

namespace App\Modules\CRM\Models;

use App\Modules\CRM\Authorization\CrmRecordVisibility;
use App\Modules\CRM\Enums\DealLostReasonEnum;
use App\Modules\CRM\Enums\DealSourceEnum;
use App\Modules\CRM\Enums\DealStageEnum;
use App\Modules\CRM\Enums\DealTypeEnum;
use App\Modules\CRM\Foundation\Enums\CurrencyCodeEnum;
use App\Modules\CRM\Foundation\ValueObjects\Money;
use App\Modules\CRM\Models\Concerns\UsesCrmPrimaryUuid;
use App\Modules\Shared\Models\User;
use Database\Factories\Modules\CRM\Models\DealFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Deal extends Model
{
    /** @use HasFactory<DealFactory> */
    use HasFactory;

    use UsesCrmPrimaryUuid;

    protected $table = 'crm_deals';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'account_id',
        'contact_id',
        'owner_id',
        'team_id',
        'stage',
        'amount_minor',
        'currency',
        'probability',
        'close_date',
        'deal_type',
        'pipeline_id',
        'lost_reason',
        'source',
    ];

    protected static function newFactory(): DealFactory
    {
        return DealFactory::new();
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'stage' => DealStageEnum::class,
            'amount_minor' => 'integer',
            'currency' => CurrencyCodeEnum::class,
            'probability' => 'integer',
            'close_date' => 'immutable_date',
            'deal_type' => DealTypeEnum::class,
            'lost_reason' => DealLostReasonEnum::class,
            'source' => DealSourceEnum::class,
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

        return $query->where('name', 'like', "%{$term}%");
    }

    /**
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    protected function scopeOpen(Builder $query): Builder
    {
        return $query->whereNotIn('stage', [
            DealStageEnum::ClosedWon,
            DealStageEnum::ClosedLost,
        ]);
    }

    /**
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    protected function scopeClosed(Builder $query): Builder
    {
        return $query->whereIn('stage', [
            DealStageEnum::ClosedWon,
            DealStageEnum::ClosedLost,
        ]);
    }

    /**
     * @return BelongsTo<Account, $this>
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * @return BelongsTo<Contact, $this>
     */
    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
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
     * @return BelongsTo<Pipeline, $this>
     */
    public function pipeline(): BelongsTo
    {
        return $this->belongsTo(Pipeline::class);
    }

    public function isClosed(): bool
    {
        return $this->stage->isClosed();
    }

    public function amountMoney(): Money
    {
        return Money::fromMinor($this->amount_minor, $this->currency);
    }

    public function expectedRevenueMoney(): Money
    {
        return Money::fromMinor(
            amountInMinorUnits: (int) floor(($this->amount_minor * $this->probability) / 100),
            currency: $this->currency,
        );
    }
}
