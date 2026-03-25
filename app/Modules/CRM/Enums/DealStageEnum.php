<?php

namespace App\Modules\CRM\Enums;

enum DealStageEnum: string
{
    case Prospecting = 'prospecting';

    case Qualification = 'qualification';

    case Proposal = 'proposal';

    case Negotiation = 'negotiation';

    case ClosedWon = 'closed_won';

    case ClosedLost = 'closed_lost';

    public function isClosed(): bool
    {
        return in_array($this, [self::ClosedWon, self::ClosedLost], true);
    }
}
