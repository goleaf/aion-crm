<?php

namespace Tests\App\Modules\CRM\Foundation\Support;

use App\Modules\CRM\Foundation\Support\DataTestId;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use Tests\Support\TestCases\UnitTestCase;

#[CoversClass(DataTestId::class)]
#[Group('crm')]
class DataTestIdUnitTest extends UnitTestCase
{
    public function test_it_builds_stable_crm_data_testid_values(): void
    {
        // Arrange

        // Act

        $testId = DataTestId::for(
            module: 'accounts',
            element: 'create-button',
            context: ['row-1'],
        );

        // Assert

        $this->assertSame('crm-accounts-create-button-row-1', $testId);
    }
}
