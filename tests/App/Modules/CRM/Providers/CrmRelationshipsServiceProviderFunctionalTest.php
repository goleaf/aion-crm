<?php

namespace Tests\App\Modules\CRM\Providers;

use App\Modules\CRM\Models\CrmTeam;
use App\Modules\CRM\Models\CrmUserProfile;
use App\Modules\CRM\Providers\CrmRelationshipsServiceProvider;
use App\Modules\Shared\Models\User;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use Tests\Support\TestCases\FunctionalTestCase;

#[CoversClass(CrmRelationshipsServiceProvider::class)]
#[Group('crm')]
class CrmRelationshipsServiceProviderFunctionalTest extends FunctionalTestCase
{
    public function test_it_registers_crm_user_relationships(): void
    {
        // Arrange

        $user = User::factory()->create();

        // Act

        $profileRelation = $user->crmProfile();
        $teamRelation = $user->crmTeam();

        // Assert

        $this->assertInstanceOf(HasOne::class, $profileRelation);
        $this->assertSame(CrmUserProfile::class, $profileRelation->getRelated()::class);

        $this->assertInstanceOf(HasOneThrough::class, $teamRelation);
        $this->assertSame(CrmTeam::class, $teamRelation->getRelated()::class);
    }
}
