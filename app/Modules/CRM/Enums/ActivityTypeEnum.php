<?php

namespace App\Modules\CRM\Enums;

enum ActivityTypeEnum: string
{
    case Meeting = 'meeting';
    case Demo = 'demo';
    case FollowUp = 'follow-up';
    case Reminder = 'reminder';

    public function label(): string
    {
        return match ($this) {
            self::Meeting => 'Meeting',
            self::Demo => 'Demo',
            self::FollowUp => 'Follow-up',
            self::Reminder => 'Reminder',
        };
    }
}
