<?php

namespace Tests\App\Modules\CRMFoundation\DataTransferObjects;

use App\Modules\CRMFoundation\DataTransferObjects\AddressData;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use Tests\Support\TestCases\UnitTestCase;

#[CoversClass(AddressData::class)]
#[Group('crm')]
class AddressDataUnitTest extends UnitTestCase
{
    public function test_it_normalizes_address_fields_from_array(): void
    {
        // Arrange

        $address = [
            'line_one' => ' 123 Main Street ',
            'line_two' => '  ',
            'city' => ' Prague ',
            'state' => '  Central Bohemia ',
            'postal_code' => ' 11000 ',
            'country_code' => ' cz ',
        ];

        // Act

        $data = AddressData::fromArray($address);

        // Assert

        $this->assertSame(
            [
                'line_one' => '123 Main Street',
                'line_two' => null,
                'city' => 'Prague',
                'state' => 'Central Bohemia',
                'postal_code' => '11000',
                'country_code' => 'CZ',
            ],
            $data->toArray(),
        );
    }
}
