<?php

namespace Tests\App\Modules\CRM\Actions;

use App\Modules\CRM\Actions\CreateAccountAction;
use App\Modules\CRM\DataTransferObjects\CreateAccountData;
use App\Modules\CRM\Enums\AccountIndustryEnum;
use App\Modules\CRM\Enums\AccountTypeEnum;
use App\Modules\CRM\Models\Account;
use App\Modules\Shared\Models\User;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use Tests\Support\TestCases\FunctionalTestCase;

#[CoversClass(CreateAccountAction::class)]
#[CoversClass(CreateAccountData::class)]
#[CoversClass(Account::class)]
#[Group('crm')]
class CreateAccountActionFunctionalTest extends FunctionalTestCase
{
    public function test_it_creates_an_account_with_normalized_tags_and_addresses(): void
    {
        // Arrange

        $owner = User::factory()->create();

        $data = new CreateAccountData(
            name: 'Acme North',
            industry: AccountIndustryEnum::Technology,
            type: AccountTypeEnum::Customer,
            website: 'https://acme.test',
            phone: '+37060000001',
            email: 'hello@acme.test',
            billingAddress: [
                'line_1' => '  10 Main Street ',
                'city' => ' Vilnius ',
                'country_code' => ' lt ',
            ],
            shippingAddress: [
                'line_1' => '  Warehouse 5 ',
                'city' => ' Kaunas ',
                'country_code' => ' lt ',
            ],
            annualRevenue: '1500000.50',
            employeeCount: 85,
            owner: $owner,
            parentAccount: null,
            tags: [' Priority Customer ', 'priority-customer', 'North America'],
            ownerTeamId: null,
        );

        // Act

        $account = resolve(CreateAccountAction::class)->execute($data);

        // Assert

        $this->assertDatabaseHas('crm_accounts', [
            'id' => $account->getKey(),
            'name' => 'Acme North',
            'industry' => AccountIndustryEnum::Technology->value,
            'type' => AccountTypeEnum::Customer->value,
            'owner_id' => $owner->id,
        ]);

        $this->assertSame(['priority-customer', 'north-america'], $account->tags);
        $this->assertSame('LT', data_get($account->billing_address, 'country_code'));
        $this->assertSame('Warehouse 5', data_get($account->shipping_address, 'line_1'));
    }
}
