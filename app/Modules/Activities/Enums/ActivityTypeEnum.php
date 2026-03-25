<?php

namespace App\Modules\Activities\Enums;

enum ActivityTypeEnum: string
{
    case Meeting = 'Meeting';
    case Task = 'Task';
    case Note = 'Note';
    case Sms = 'SMS';

    public function label(): string
    {
        return $this->value;
    }
}
