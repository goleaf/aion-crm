<?php

namespace App\Modules\CRM\Enums;

enum LeadSourceEnum: string
{
    case WalkIn = 'walk_in';

    case ColdCall = 'cold_call';

    case Referral = 'referral';

    case InternalForm = 'internal_form';

    case Event = 'event';

    public function label(): string
    {
        return match ($this) {
            self::WalkIn => 'Walk-in',
            self::ColdCall => 'Cold Call',
            self::Referral => 'Referral',
            self::InternalForm => 'Internal Form',
            self::Event => 'Event',
        };
    }
}
