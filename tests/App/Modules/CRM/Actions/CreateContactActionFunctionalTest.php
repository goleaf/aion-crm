<?php

namespace Tests\App\Modules\CRM\Actions;

use App\Modules\CRM\Actions\CreateContactAction;
use App\Modules\CRM\DataTransferObjects\CreateContactData;
use App\Modules\CRM\Enums\ContactLeadSourceEnum;
use App\Modules\CRM\Enums\ContactPreferredChannelEnum;
use App\Modules\CRM\Models\Account;
use App\Modules\CRM\Models\Contact;
use App\Modules\Shared\Models\User;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use Tests\Support\TestCases\FunctionalTestCase;

#[CoversClass(CreateContactAction::class)]
#[CoversClass(CreateContactData::class)]
#[CoversClass(Contact::class)]
#[Group('crm')]
class CreateContactActionFunctionalTest extends FunctionalTestCase
{
    public function test_it_creates_a_contact_for_an_account_owner_pair(): void
    {
        // Arrange

        $owner = User::factory()->create();
        $account = Account::factory()->for($owner, 'owner')->create();

        $data = new CreateContactData(
            firstName: 'Ava',
            lastName: 'Stone',
            email: 'ava.stone@acme.test',
            phone: '+37060000002',
            mobile: '+37060000003',
            jobTitle: 'Operations Director',
            department: 'Operations',
            account: $account,
            owner: $owner,
            leadSource: ContactLeadSourceEnum::Referral,
            doNotContact: false,
            birthday: '1989-11-05',
            preferredChannel: ContactPreferredChannelEnum::Phone,
            notes: 'Key buying committee stakeholder.',
            ownerTeamId: null,
        );

        // Act

        $contact = resolve(CreateContactAction::class)->execute($data);

        // Assert

        $this->assertDatabaseHas('crm_contacts', [
            'id' => $contact->getKey(),
            'first_name' => 'Ava',
            'last_name' => 'Stone',
            'account_id' => $account->getKey(),
            'owner_id' => $owner->id,
            'lead_source' => ContactLeadSourceEnum::Referral->value,
            'preferred_channel' => ContactPreferredChannelEnum::Phone->value,
            'do_not_contact' => false,
        ]);
    }
}
