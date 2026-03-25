<?php

use App\Modules\CRMFoundation\Contracts\WorkspaceContextContract;
use App\Modules\CRMFoundation\Enums\CurrencyCodeEnum;
use App\Modules\CRMFoundation\Enums\OwnershipTypeEnum;
use App\Modules\CRMFoundation\Enums\RecordVisibilityEnum;

return [
    'tenant' => [
        'enabled' => false,
        'column' => 'workspace_id',
    ],

    'test_id' => [
        'prefix' => 'crm',
    ],

    'uuid' => [
        'column' => 'id',
        'id_column' => 'id',
        'generator' => 'uuid_v4',
        'owner_foreign_key' => 'owner_id',
    ],

    'ownership' => [
        'owner_column' => 'owner_id',
        'team_column' => 'team_id',
        'default_type' => OwnershipTypeEnum::User->value,
        'visibility_levels' => [
            RecordVisibilityEnum::Own->value,
            RecordVisibilityEnum::Team->value,
            RecordVisibilityEnum::All->value,
        ],
    ],

    'address' => [
        'required_fields' => ['line_one', 'city', 'country_code'],
    ],

    'money' => [
        'minor_unit_scale' => 2,
        'default_currency' => CurrencyCodeEnum::Usd->value,
        'supported_currencies' => [
            CurrencyCodeEnum::Usd->value,
            CurrencyCodeEnum::Eur->value,
            CurrencyCodeEnum::Gbp->value,
        ],
    ],

    'tags' => [
        'max_tags' => 25,
        'max_length' => 32,
    ],

    'test_ids' => [
        'prefix' => 'crm',
    ],

    'tenancy' => [
        'workspace_column' => 'workspace_id',
        'context_contract' => WorkspaceContextContract::class,
    ],
];
