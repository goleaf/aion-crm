<?php

namespace App\Modules\CRM\DataTransferObjects;

use App\Modules\CRM\Enums\LeadRatingEnum;

final readonly class LeadScoreData
{
    public function __construct(
        public int $score,
        public LeadRatingEnum $rating,
    ) {}
}
