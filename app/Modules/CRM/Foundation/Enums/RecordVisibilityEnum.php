<?php

namespace App\Modules\CRM\Foundation\Enums;

enum RecordVisibilityEnum: string
{
    case Own = 'own';

    case Team = 'team';

    case All = 'all';
}
