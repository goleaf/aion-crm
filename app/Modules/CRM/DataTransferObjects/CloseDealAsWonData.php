<?php

namespace App\Modules\CRM\DataTransferObjects;

use App\Modules\CRM\Models\Deal;

final readonly class CloseDealAsWonData
{
    public function __construct(
        public Deal $deal,
    ) {}
}
