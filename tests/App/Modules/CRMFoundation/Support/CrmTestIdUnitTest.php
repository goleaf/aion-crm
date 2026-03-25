<?php

namespace Tests\App\Modules\CRMFoundation\Support;

use App\Modules\CRMFoundation\Support\CrmTestId;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use Tests\Support\TestCases\UnitTestCase;

#[CoversClass(CrmTestId::class)]
#[Group('crm')]
class CrmTestIdUnitTest extends UnitTestCase
{
    public function test_it_builds_element_test_ids_using_the_naming_convention(): void
    {
        // Arrange

        // Act

        $testId = CrmTestId::forElement('Accounts List', 'Create Button', 'crm');

        // Assert

        $this->assertSame('crm-accounts-list-create-button', $testId);
    }

    public function test_it_builds_row_test_ids_with_record_identifier(): void
    {
        // Arrange

        // Act

        $testId = CrmTestId::forRow('Contacts', 42, 'crm');

        // Assert

        $this->assertSame('crm-contacts-row-42', $testId);
    }
}
