<?php

namespace Tests\App\Modules\CRM\Foundation\Enums;

use App\Modules\CRM\Foundation\Enums\OwnershipTypeEnum;
use App\Modules\CRM\Foundation\Enums\RecordStatusEnum;
use App\Modules\CRM\Foundation\Enums\RecordVisibilityEnum;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use Tests\Support\TestCases\UnitTestCase;

#[CoversClass(OwnershipTypeEnum::class)]
#[CoversClass(RecordVisibilityEnum::class)]
#[CoversClass(RecordStatusEnum::class)]
#[Group('crm')]
class CrmFoundationEnumsUnitTest extends UnitTestCase
{
    public function test_it_exposes_stable_ownership_type_values(): void
    {
        // Arrange

        $expected = ['user', 'team'];

        // Act

        $values = array_column(OwnershipTypeEnum::cases(), 'value');

        // Assert

        $this->assertSame($expected, $values);
    }

    public function test_it_exposes_stable_record_visibility_values(): void
    {
        // Arrange

        $expected = ['own', 'team', 'all'];

        // Act

        $values = array_column(RecordVisibilityEnum::cases(), 'value');

        // Assert

        $this->assertSame($expected, $values);
    }

    public function test_it_exposes_stable_record_status_values(): void
    {
        // Arrange

        $expected = ['active', 'archived'];

        // Act

        $values = array_column(RecordStatusEnum::cases(), 'value');

        // Assert

        $this->assertSame($expected, $values);
    }
}
