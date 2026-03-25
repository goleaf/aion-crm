<?php

namespace Tests\App\Modules\CRMFoundation\DataTransferObjects;

use App\Modules\CRMFoundation\DataTransferObjects\TagsData;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use Tests\Support\TestCases\UnitTestCase;

#[CoversClass(TagsData::class)]
#[Group('crm')]
class TagsDataUnitTest extends UnitTestCase
{
    public function test_it_normalizes_and_deduplicates_tags(): void
    {
        // Arrange

        $tags = [' Enterprise ', 'enterprise', 'Q4 Priority', 'Q4@Priority', ''];

        // Act

        $data = TagsData::fromArray($tags);

        // Assert

        $this->assertSame(['enterprise', 'q4-priority'], $data->toArray());
        $this->assertTrue($data->contains('Q4 Priority'));
    }

    public function test_it_limits_tag_count(): void
    {
        // Arrange

        $tags = ['one', 'two', 'three'];

        // Act

        $data = TagsData::fromArray($tags, maxTags: 2);

        // Assert

        $this->assertSame(['one', 'two'], $data->toArray());
    }
}
