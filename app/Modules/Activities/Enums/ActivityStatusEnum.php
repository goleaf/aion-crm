<?php

namespace App\Modules\Activities\Enums;

enum ActivityStatusEnum: string
{
    case Planned = 'Planned';
    case Completed = 'Completed';
    case Cancelled = 'Cancelled';
}
