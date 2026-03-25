<?php

namespace Tests\App\Modules\CRM\Foundation\ValueObjects;

use App\Modules\CRM\Foundation\ValueObjects\PostalAddress;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use Tests\Support\TestCases\UnitTestCase;

#[CoversClass(PostalAddress::class)]
#[Group('crm')]
class PostalAddressUnitTest extends UnitTestCase
{
    public function test_it_normalizes_address_payload_from_array(): void
    {
        // Arrange

        $payload = [
            'line_1' => '  10 Main Street ',
            'line_2' => '  ',
            'city' => '  Prague ',
            'state_or_province' => '  Prague ',
            'postal_code' => ' 110 00 ',
            'country_code' => ' cz ',
        ];

        // Act

        $address = PostalAddress::fromArray($payload);

        // Assert

        $this->assertSame('10 Main Street', $address->lineOne);
        $this->assertNull($address->lineTwo);
        $this->assertSame('Prague', $address->city);
        $this->assertSame('Prague', $address->stateOrProvince);
        $this->assertSame('110 00', $address->postalCode);
        $this->assertSame('CZ', $address->countryCode);
    }

    public function test_it_converts_to_stable_array_shape(): void
    {
        // Arrange

        $address = PostalAddress::fromArray([
            'line_1' => '10 Main Street',
            'city' => 'Prague',
            'country_code' => 'CZ',
        ]);

        // Act

        $result = $address->toArray();

        // Assert

        $this->assertSame(
            [
                'line_1' => '10 Main Street',
                'line_2' => null,
                'city' => 'Prague',
                'state_or_province' => null,
                'postal_code' => null,
                'country_code' => 'CZ',
            ],
            $result,
        );
    }
}
