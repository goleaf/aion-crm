<?php

namespace App\Modules\CRMFoundation\Contracts;

interface WorkspaceContextContract
{
    public function workspaceId(): ?string;
}
