<?php

namespace App\Modules\CRM\Enums;

enum DealLostReasonEnum: string
{
    case Price = 'price';

    case Competitor = 'competitor';

    case NoBudget = 'no_budget';

    case NoDecision = 'no_decision';
}
