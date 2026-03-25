<?php

namespace App\Modules\CRM\Models\Concerns;

use App\Modules\CRM\Foundation\Support\UuidPolicy;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

trait UsesCrmPrimaryUuid
{
    use HasUuids;

    public $incrementing = false;

    protected $keyType = 'string';

    public function newUniqueId(): string
    {
        return UuidPolicy::generate();
    }
}
