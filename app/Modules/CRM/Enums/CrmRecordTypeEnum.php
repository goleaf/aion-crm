<?php

namespace App\Modules\CRM\Enums;

use App\Modules\CRM\Models\Account;
use App\Modules\CRM\Models\Contact;
use App\Modules\CRM\Models\Deal;
use App\Modules\CRM\Models\Lead;

enum CrmRecordTypeEnum: string
{
    case Account = 'account';
    case Contact = 'contact';
    case Deal = 'deal';
    case Lead = 'lead';

    public function label(): string
    {
        return match ($this) {
            self::Account => 'Account',
            self::Contact => 'Contact',
            self::Deal => 'Deal',
            self::Lead => 'Lead',
        };
    }

    /**
     * @return class-string<Account|Contact|Deal|Lead>
     */
    public function modelClass(): string
    {
        return match ($this) {
            self::Account => Account::class,
            self::Contact => Contact::class,
            self::Deal => Deal::class,
            self::Lead => Lead::class,
        };
    }

    public function keyName(): string
    {
        return match ($this) {
            self::Lead => 'lead_id',
            default => 'id',
        };
    }

    /**
     * @return list<string>
     */
    public function selectColumns(): array
    {
        return match ($this) {
            self::Lead => ['lead_id', 'first_name', 'last_name'],
            default => ['id', 'name'],
        };
    }
}
