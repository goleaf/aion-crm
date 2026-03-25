<?php

namespace App\Modules\CRM\Enums;

use App\Modules\CRM\Foundation\Enums\RecordVisibilityEnum;

enum CrmRoleEnum: string
{
    case Admin = 'admin';

    case Manager = 'manager';

    case Rep = 'rep';

    case Viewer = 'viewer';

    public function defaultRecordVisibility(): RecordVisibilityEnum
    {
        return match ($this) {
            self::Admin => RecordVisibilityEnum::All,
            self::Manager => RecordVisibilityEnum::Team,
            self::Rep, self::Viewer => RecordVisibilityEnum::Own,
        };
    }

    public function canManageReassignments(): bool
    {
        return match ($this) {
            self::Admin, self::Manager => true,
            self::Rep, self::Viewer => false,
        };
    }
}
