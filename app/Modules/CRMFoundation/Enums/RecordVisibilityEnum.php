<?php

namespace App\Modules\CRMFoundation\Enums;

enum RecordVisibilityEnum: string
{
    case Own = 'own';

    case Team = 'team';

    case All = 'all';
}
