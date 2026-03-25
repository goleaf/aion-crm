<?php

namespace Tests\App\Modules\CRM\Foundation\Support;

use App\Modules\CRM\Foundation\Support\UuidPolicy;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use Tests\Support\TestCases\UnitTestCase;

#[CoversClass(UuidPolicy::class)]
#[Group('crm')]
class UuidPolicyUnitTest extends UnitTestCase
{
    public function test_it_generates_valid_uuid_values_for_crm_primary_keys(): void
    {
        // Arrange

        // Act

        $first = UuidPolicy::generate();
        $second = UuidPolicy::generate();

        // Assert

        $this->assertNotSame($first, $second);
        $this->assertTrue(UuidPolicy::isValid($first));
        $this->assertTrue(UuidPolicy::isValid($second));
    }
}
