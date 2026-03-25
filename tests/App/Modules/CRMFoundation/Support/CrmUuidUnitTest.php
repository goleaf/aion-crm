<?php

namespace Tests\App\Modules\CRMFoundation\Support;

use App\Modules\CRMFoundation\Support\CrmUuid;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use Tests\Support\TestCases\UnitTestCase;

#[CoversClass(CrmUuid::class)]
#[Group('crm')]
class CrmUuidUnitTest extends UnitTestCase
{
    public function test_it_generates_valid_uuids_for_crm_records(): void
    {
        // Arrange

        // Act

        $uuid = CrmUuid::generate();

        // Assert

        $this->assertTrue(CrmUuid::isValid($uuid));
    }

    public function test_it_detects_invalid_uuid_values(): void
    {
        // Arrange

        $invalidValue = 'crm-record-123';

        // Act

        $isValid = CrmUuid::isValid($invalidValue);

        // Assert

        $this->assertFalse($isValid);
    }
}
