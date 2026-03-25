<?php

namespace Tests\App\Modules\CRM\Foundation\Support;

use App\Modules\CRM\Foundation\Support\TagList;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use Tests\Support\TestCases\UnitTestCase;

#[CoversClass(TagList::class)]
#[Group('crm')]
class TagListUnitTest extends UnitTestCase
{
    public function test_it_normalizes_tag_lists_to_stable_slug_values(): void
    {
        // Arrange

        $tags = [' VIP Customer ', 'vip customer', 'Q4 🔥', '', 'Team-A'];

        // Act

        $result = TagList::normalize($tags);

        // Assert

        $this->assertSame(['vip-customer', 'q4', 'team-a'], $result);
    }
}
