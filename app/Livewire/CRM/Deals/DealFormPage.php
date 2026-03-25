<?php

namespace App\Livewire\CRM\Deals;

use App\Http\Api\CRM\Requests\StoreDealRequest;
use App\Http\Api\CRM\Requests\UpdateDealRequest;
use App\Modules\CRM\Actions\CreateDealAction;
use App\Modules\CRM\Actions\UpdateDealAction;
use App\Modules\CRM\Authorization\CrmRecordManagement;
use App\Modules\CRM\Authorization\CrmRecordVisibility;
use App\Modules\CRM\DataTransferObjects\CreateDealData;
use App\Modules\CRM\DataTransferObjects\UpdateDealData;
use App\Modules\CRM\Enums\DealLostReasonEnum;
use App\Modules\CRM\Enums\DealSourceEnum;
use App\Modules\CRM\Enums\DealStageEnum;
use App\Modules\CRM\Enums\DealTypeEnum;
use App\Modules\CRM\Exceptions\DealLifecycleViolationException;
use App\Modules\CRM\Foundation\Enums\CurrencyCodeEnum;
use App\Modules\CRM\Models\Account;
use App\Modules\CRM\Models\Contact;
use App\Modules\CRM\Models\CrmUserProfile;
use App\Modules\CRM\Models\Deal;
use App\Modules\CRM\Models\Pipeline;
use App\Modules\Shared\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class DealFormPage extends Component
{
    public ?string $dealId = null;

    public string $name = '';

    public string $accountId = '';

    public string $contactId = '';

    public string $ownerId = '';

    public string $stage = '';

    public string $amount = '';

    public string $currency = '';

    public int $probability = 50;

    public string $closeDate = '';

    public string $dealType = '';

    public string $pipelineId = '';

    public string $lostReason = '';

    public string $source = '';

    public function mount(?Deal $deal = null): void
    {
        $actor = auth()->user();
        assert($actor instanceof User);

        if ($deal instanceof Deal) {
            abort_unless(CrmRecordManagement::canManageRecord($actor, $deal->owner_id, $deal->team_id), 403);

            $this->dealId = $deal->getKey();
            $this->name = $deal->name;
            $this->accountId = $deal->account_id;
            $this->contactId = $deal->contact_id ?? '';
            $this->ownerId = (string) $deal->owner_id;
            $this->stage = $deal->stage->value;
            $this->amount = $deal->amountMoney()->toDecimal();
            $this->currency = $deal->currency->value;
            $this->probability = $deal->probability;
            $this->closeDate = $deal->close_date?->toDateString() ?? '';
            $this->dealType = $deal->deal_type->value;
            $this->pipelineId = $deal->pipeline_id;
            $this->lostReason = $deal->lost_reason?->value ?? '';
            $this->source = $deal->source?->value ?? '';

            return;
        }

        $defaultPipeline = Pipeline::query()
            ->ordered()
            ->first();

        $this->ownerId = (string) $actor->id;
        $this->stage = DealStageEnum::Prospecting->value;
        $this->currency = CurrencyCodeEnum::USD->value;
        $this->dealType = DealTypeEnum::NewBusiness->value;
        $this->pipelineId = $defaultPipeline?->getKey() ?? '';
    }

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return $this->dealId === null
            ? (new StoreDealRequest)->rules()
            : (new UpdateDealRequest)->rules();
    }

    public function save(): void
    {
        $actor = auth()->user();
        assert($actor instanceof User);

        $this->validate();

        $owner = User::query()->findOrFail($this->ownerId);

        abort_unless(CrmRecordManagement::canAssignRecordToOwner($actor, $owner), 403);

        try {
            if ($this->dealId === null) {
                resolve(CreateDealAction::class)->execute($this->makeCreateData());
            } else {
                $deal = Deal::query()->findOrFail($this->dealId);

                abort_unless(CrmRecordManagement::canManageRecord($actor, $deal->owner_id, $deal->team_id), 403);

                resolve(UpdateDealAction::class)->execute($this->makeUpdateData($deal));
            }
        } catch (DealLifecycleViolationException $exception) {
            throw ValidationException::withMessages([
                'stage' => [$exception->getMessage()],
            ]);
        }

        $this->redirectRoute('crm.deals.index', navigate: true);
    }

    private function makeCreateData(): CreateDealData
    {
        return new CreateDealData(
            name: $this->name,
            account: Account::query()->findOrFail($this->accountId),
            contact: $this->contactId === '' ? null : Contact::query()->findOrFail($this->contactId),
            owner: User::query()->findOrFail($this->ownerId),
            stage: DealStageEnum::from($this->stage),
            amount: $this->amount,
            currency: CurrencyCodeEnum::from($this->currency),
            probability: $this->probability,
            closeDate: $this->closeDate === '' ? null : CarbonImmutable::parse($this->closeDate),
            dealType: DealTypeEnum::from($this->dealType),
            pipeline: Pipeline::query()->findOrFail($this->pipelineId),
            lostReason: $this->lostReason === '' ? null : DealLostReasonEnum::from($this->lostReason),
            source: $this->source === '' ? null : DealSourceEnum::from($this->source),
        );
    }

    private function makeUpdateData(Deal $deal): UpdateDealData
    {
        return new UpdateDealData(
            deal: $deal,
            name: $this->name,
            account: Account::query()->findOrFail($this->accountId),
            contact: $this->contactId === '' ? null : Contact::query()->findOrFail($this->contactId),
            owner: User::query()->findOrFail($this->ownerId),
            stage: DealStageEnum::from($this->stage),
            amount: $this->amount,
            currency: CurrencyCodeEnum::from($this->currency),
            probability: $this->probability,
            closeDate: $this->closeDate === '' ? null : CarbonImmutable::parse($this->closeDate),
            dealType: DealTypeEnum::from($this->dealType),
            pipeline: Pipeline::query()->findOrFail($this->pipelineId),
            lostReason: $this->lostReason === '' ? null : DealLostReasonEnum::from($this->lostReason),
            source: $this->source === '' ? null : DealSourceEnum::from($this->source),
        );
    }

    public function render(): View
    {
        $actor = auth()->user();
        assert($actor instanceof User);

        $ownerProfiles = CrmRecordVisibility::applyToQuery(
            CrmUserProfile::query()
                ->select(['id', 'user_id', 'primary_team_id', 'role', 'record_visibility', 'is_active', 'deactivated_at'])
                ->with('user:id,name'),
            $actor,
            'user_id',
            'primary_team_id',
        )->get();

        return view('livewire.c-r-m.deals.deal-form-page', [
            'accounts' => Account::query()
                ->select(['id', 'name', 'owner_id', 'team_id'])
                ->visibleTo($actor)
                ->orderBy('name')
                ->get(),
            'contacts' => Contact::query()
                ->select(['id', 'account_id', 'owner_id', 'team_id', 'first_name', 'last_name', 'email'])
                ->visibleTo($actor)
                ->when($this->accountId !== '', fn ($query) => $query->where('account_id', $this->accountId))
                ->orderBy('last_name')
                ->orderBy('first_name')
                ->get(),
            'owners' => $ownerProfiles
                ->pluck('user')
                ->filter()
                ->unique('id')
                ->values(),
            'pipelines' => Pipeline::query()
                ->select(['id', 'name', 'position'])
                ->ordered()
                ->get(),
            'stageOptions' => DealStageEnum::cases(),
            'currencyOptions' => CurrencyCodeEnum::cases(),
            'dealTypeOptions' => DealTypeEnum::cases(),
            'lostReasonOptions' => DealLostReasonEnum::cases(),
            'sourceOptions' => DealSourceEnum::cases(),
            'isEditing' => $this->dealId !== null,
        ]);
    }
}
