<?php

namespace App\Livewire\CRM\Deals;

use App\Modules\CRM\Enums\DealStageEnum;
use App\Modules\CRM\Models\Deal;
use App\Modules\CRM\Models\Pipeline;
use App\Modules\Shared\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class DealsIndexPage extends Component
{
    public string $search = '';

    public string $stage = '';

    public string $pipelineId = '';

    public bool $showClosed = false;

    public function render(): View
    {
        $actor = auth()->user();
        assert($actor instanceof User);

        return view('livewire.c-r-m.deals.deals-index-page', [
            'deals' => Deal::query()
                ->select([
                    'id',
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
                    'created_at',
                ])
                ->with([
                    'account:id,name',
                    'contact:id,first_name,last_name,email',
                    'owner:id,name',
                    'pipeline:id,name',
                ])
                ->visibleTo($actor)
                ->search($this->search)
                ->when($this->stage !== '', fn ($query) => $query->where('stage', $this->stage))
                ->when($this->pipelineId !== '', fn ($query) => $query->where('pipeline_id', $this->pipelineId))
                ->unless($this->showClosed, fn ($query) => $query->open())
                ->orderBy('close_date')
                ->orderByDesc('created_at')
                ->get(),
            'pipelines' => Pipeline::query()
                ->select(['id', 'name', 'position'])
                ->ordered()
                ->get(),
            'stageOptions' => DealStageEnum::cases(),
        ]);
    }
}
