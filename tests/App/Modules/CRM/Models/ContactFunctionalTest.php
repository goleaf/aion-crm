<?php

namespace Tests\App\Modules\CRM\Models;

use App\Modules\CRM\Foundation\Support\UuidPolicy;
use App\Modules\CRM\Models\Account;
use App\Modules\CRM\Models\Contact;
use App\Modules\Shared\Models\User;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use Tests\Support\TestCases\FunctionalTestCase;

#[CoversClass(Contact::class)]
#[Group('crm')]
class ContactFunctionalTest extends FunctionalTestCase
{
    public function test_it_belongs_to_an_account_and_owner(): void
    {
        // Arrange

        $owner = User::factory()->create();
        $account = Account::factory()->for($owner, 'owner')->create();

        $contact = Contact::factory()
            ->for($account)
            ->for($owner, 'owner')
            ->create([
                'do_not_contact' => true,
            ]);

        // Act

        $contact->load(['account', 'owner']);

        // Assert

        $this->assertTrue(UuidPolicy::isValid($contact->getKey()));
        $this->assertSame($account->getKey(), $contact->account?->getKey());
        $this->assertSame($owner->id, $contact->owner?->id);
        $this->assertTrue($contact->do_not_contact);
    }
}
