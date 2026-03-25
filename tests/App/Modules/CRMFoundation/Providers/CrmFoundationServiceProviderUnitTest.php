<?php

namespace Tests\App\Modules\CRMFoundation\Providers;

use App\Modules\CRMFoundation\Contracts\WorkspaceContextContract;
use App\Modules\CRMFoundation\Providers\CrmFoundationServiceProvider;
use App\Modules\CRMFoundation\Tenancy\NullWorkspaceContext;
use Illuminate\Container\Container;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use Tests\Support\TestCases\UnitTestCase;

#[CoversClass(CrmFoundationServiceProvider::class)]
#[CoversClass(NullWorkspaceContext::class)]
#[Group('crm')]
class CrmFoundationServiceProviderUnitTest extends UnitTestCase
{
    public function test_it_binds_the_default_workspace_context(): void
    {
        // Arrange

        $container = new Container;
        $provider = new CrmFoundationServiceProvider($container);

        // Act

        $provider->register();
        $workspaceContext = $container->make(WorkspaceContextContract::class);

        // Assert

        $this->assertInstanceOf(NullWorkspaceContext::class, $workspaceContext);
        $this->assertNull($workspaceContext->workspaceId());
    }

    public function test_it_exposes_crm_foundation_conventions_in_config(): void
    {
        // Arrange

        $configPath = dirname(__DIR__, 5).'/config/crm-foundation.php';

        // Act

        $config = require $configPath;

        // Assert

        $this->assertSame('uuid_v4', $config['uuid']['generator']);
        $this->assertSame(['own', 'team', 'all'], $config['ownership']['visibility_levels']);
        $this->assertSame('workspace_id', $config['tenancy']['workspace_column']);
        $this->assertSame('crm', $config['test_ids']['prefix']);
        $this->assertSame('USD', $config['money']['default_currency']);
        $this->assertSame(['USD', 'EUR', 'GBP'], $config['money']['supported_currencies']);
        $this->assertSame(25, $config['tags']['max_tags']);
    }
}
