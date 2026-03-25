<?php

namespace App\Livewire\CRM\Leads;

use App\Http\Web\CRM\Requests\UpsertLeadRequest;
use App\Modules\CRM\Actions\CreateLeadAction;
use App\Modules\CRM\Actions\UpdateLeadAction;
use App\Modules\CRM\DataTransferObjects\CreateLeadData;
use App\Modules\CRM\DataTransferObjects\UpdateLeadData;
use App\Modules\CRM\Enums\LeadRatingEnum;
use App\Modules\CRM\Enums\LeadSourceEnum;
use App\Modules\CRM\Enums\LeadStatusEnum;
use App\Modules\CRM\Models\Lead;
use App\Modules\Shared\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class LeadsIndexPage extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public string $statusFilter = 'all';

    #[Url]
    public string $sourceFilter = 'all';

    #[Url]
    public string $ratingFilter = 'all';

    #[Url]
    public string $ownerFilter = 'all';

    #[Url]
    public string $convertedFilter = 'all';

    public ?string $editingLeadId = null;

    public string $first_name = '';

    public ?string $last_name = null;

    public ?string $company = null;

    public ?string $email = null;

    public ?string $phone = null;

    public string $lead_source = '';

    public string $status = '';

    public int|string|null $campaign_id = null;

    public int|string $owner_id = 0;

    public ?string $description = null;

    public ?string $feedback = null;

    public ?int $current_score = null;

    public ?string $current_rating = null;

    public bool $current_converted = false;

    public ?string $current_converted_to_contact_id = null;

    public ?string $current_converted_to_deal_id = null;

    public ?string $current_converted_at = null;

    public function mount(): void
    {
        Gate::authorize('viewAny', Lead::class);

        $this->resetForm();
    }

    protected function rules(): array
    {
        return (new UpsertLeadRequest)->rules();
    }

    #[Computed]
    public function leads()
    {
        Gate::authorize('viewAny', Lead::class);

        return Lead::query()
            ->listView()
            ->searchTerm($this->search)
            ->withStatus($this->statusFilter)
            ->fromSource($this->sourceFilter)
            ->withRating($this->ratingFilter)
            ->ownedBy($this->ownerFilter)
            ->converted($this->convertedFilter)
            ->latest('created_at')
            ->paginate(10);
    }

    #[Computed]
    public function owners(): Collection
    {
        return User::query()
            ->select(['id', 'name'])
            ->orderBy('name')
            ->get();
    }

    /**
     * @return list<LeadSourceEnum>
     */
    public function leadSourceOptions(): array
    {
        return LeadSourceEnum::cases();
    }

    /**
     * @return list<LeadStatusEnum>
     */
    public function leadStatusOptions(): array
    {
        return array_filter(
            LeadStatusEnum::cases(),
            fn (LeadStatusEnum $status): bool => $status !== LeadStatusEnum::Converted,
        );
    }

    /**
     * @return list<LeadRatingEnum>
     */
    public function leadRatingOptions(): array
    {
        return LeadRatingEnum::cases();
    }

    public function editLead(string $leadId): void
    {
        $lead = Lead::query()->findOrFail($leadId);

        Gate::authorize('update', $lead);

        $this->editingLeadId = $lead->lead_id;
        $this->first_name = $lead->first_name;
        $this->last_name = $lead->last_name;
        $this->company = $lead->company;
        $this->email = $lead->email;
        $this->phone = $lead->phone;
        $this->lead_source = $lead->lead_source->value;
        $this->status = $lead->status->value;
        $this->campaign_id = $lead->campaign_id;
        $this->owner_id = $lead->owner_id;
        $this->description = $lead->description;
        $this->feedback = null;
        $this->syncLeadSummary($lead);
    }

    public function createNew(): void
    {
        Gate::authorize('create', Lead::class);

        $this->resetForm();
    }

    public function save(): void
    {
        $this->normalizeNullableInputs();

        $validated = $this->validate();

        if ($this->editingLeadId === null) {
            Gate::authorize('create', Lead::class);

            resolve(CreateLeadAction::class)->execute(
                CreateLeadData::fromValidated($validated),
            );

            $this->feedback = 'Lead created.';
        } else {
            $lead = Lead::query()->findOrFail($this->editingLeadId);

            Gate::authorize('update', $lead);

            resolve(UpdateLeadAction::class)->execute(
                UpdateLeadData::fromLead($lead, $validated),
            );

            $this->feedback = 'Lead updated.';
        }

        $this->resetForm(keepFeedback: true);
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatedSourceFilter(): void
    {
        $this->resetPage();
    }

    public function updatedRatingFilter(): void
    {
        $this->resetPage();
    }

    public function updatedOwnerFilter(): void
    {
        $this->resetPage();
    }

    public function updatedConvertedFilter(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        return view('livewire.c-r-m.leads.leads-index-page');
    }

    private function resetForm(bool $keepFeedback = false): void
    {
        $feedback = $this->feedback;

        $this->editingLeadId = null;
        $this->first_name = '';
        $this->last_name = null;
        $this->company = null;
        $this->email = null;
        $this->phone = null;
        $this->lead_source = LeadSourceEnum::InternalForm->value;
        $this->status = LeadStatusEnum::New->value;
        $this->campaign_id = null;
        $this->owner_id = auth()->guard('web')->id() ?? 0;
        $this->description = null;
        $this->current_score = null;
        $this->current_rating = null;
        $this->current_converted = false;
        $this->current_converted_to_contact_id = null;
        $this->current_converted_to_deal_id = null;
        $this->current_converted_at = null;

        if ($keepFeedback) {
            $this->feedback = $feedback;

            return;
        }

        $this->feedback = null;
    }

    private function normalizeNullableInputs(): void
    {
        $this->last_name = $this->nullIfBlank($this->last_name);
        $this->company = $this->nullIfBlank($this->company);
        $this->email = $this->nullIfBlank($this->email);
        $this->phone = $this->nullIfBlank($this->phone);
        $this->description = $this->nullIfBlank($this->description);

        if ($this->campaign_id === '') {
            $this->campaign_id = null;
        }
    }

    private function nullIfBlank(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $trimmedValue = trim($value);

        return $trimmedValue === '' ? null : $trimmedValue;
    }

    private function syncLeadSummary(Lead $lead): void
    {
        $this->current_score = $lead->score;
        $this->current_rating = $lead->rating->label();
        $this->current_converted = $lead->converted;
        $this->current_converted_to_contact_id = $lead->converted_to_contact_id;
        $this->current_converted_to_deal_id = $lead->converted_to_deal_id;
        $this->current_converted_at = $lead->converted_at?->format('Y-m-d H:i');
    }
}
