<?php

namespace Tests\App\Modules\CRM\Models;

use App\Modules\CRM\Foundation\Support\UuidPolicy;
use App\Modules\CRM\Models\Account;
use App\Modules\CRM\Models\Contact;
use App\Modules\Shared\Models\User;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use Tests\Support\TestCases\FunctionalTestCase;

#[CoversClass(Account::class)]
#[Group('crm')]
class AccountFunctionalTest extends FunctionalTestCase
{
    public function test_it_uses_uuid_primary_keys_and_resolves_relationships(): void
    {
        // Arrange

        $owner = User::factory()->create();
        $parentAccount = Account::factory()->for($owner, 'owner')->create();

        $account = Account::factory()
            ->for($owner, 'owner')
            ->for($parentAccount, 'parentAccount')
            ->create();

        $contact = Contact::factory()
            ->for($account)
            ->for($owner, 'owner')
            ->create();

        // Act

        $account->load(['owner', 'parentAccount', 'contacts']);

        // Assert

        $this->assertTrue(UuidPolicy::isValid($account->getKey()));
        $this->assertSame($owner->id, $account->owner?->id);
        $this->assertSame($parentAccount->getKey(), $account->parentAccount?->getKey());
        $this->assertCount(1, $account->contacts);
        $this->assertSame($contact->getKey(), $account->contacts->sole()->getKey());
    }
}
