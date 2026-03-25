<?php

namespace App\Modules\CRMFoundation\Tenancy;

use App\Modules\CRMFoundation\Contracts\WorkspaceContextContract;

final class NullWorkspaceContext implements WorkspaceContextContract
{
    public function workspaceId(): ?string
    {
        return null;
    }
}
