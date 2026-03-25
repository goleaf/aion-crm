<?php

namespace Tests\App\Modules\CRM\Foundation\Configuration;

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\Group;
use Tests\Support\TestCases\UnitTestCase;

#[CoversNothing]
#[Group('crm')]
class CrmFoundationConfigUnitTest extends UnitTestCase
{
    public function test_it_exposes_future_tenant_seam_conventions_without_enabling_multitenancy(): void
    {
        // Arrange

        $config = require __DIR__.'/../../../../../../config/crm-foundation.php';

        // Act

        $tenantEnabled = $config['tenant']['enabled'] ?? null;
        $tenantColumn = $config['tenant']['column'] ?? null;

        // Assert

        $this->assertFalse($tenantEnabled);
        $this->assertSame('workspace_id', $tenantColumn);
    }
}
