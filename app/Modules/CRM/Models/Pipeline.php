<?php

namespace App\Modules\CRM\Models;

use App\Modules\CRM\Models\Concerns\UsesCrmPrimaryUuid;
use Database\Factories\Modules\CRM\Models\PipelineFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pipeline extends Model
{
    /** @use HasFactory<PipelineFactory> */
    use HasFactory;

    use UsesCrmPrimaryUuid;

    protected $table = 'crm_pipelines';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'position',
        'is_default',
    ];

    protected static function newFactory(): PipelineFactory
    {
        return PipelineFactory::new();
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'position' => 'integer',
            'is_default' => 'boolean',
        ];
    }

    /**
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    protected function scopeOrdered(Builder $query): Builder
    {
        return $query
            ->orderBy('position')
            ->orderBy('name');
    }

    /**
     * @return HasMany<Deal, $this>
     */
    public function deals(): HasMany
    {
        return $this->hasMany(Deal::class);
    }
}
