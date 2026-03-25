<?php

namespace App\Modules\CRM\Enums;

enum AccountIndustryEnum: string
{
    case Technology = 'technology';
    case Finance = 'finance';
    case Retail = 'retail';
    case Healthcare = 'healthcare';
    case Manufacturing = 'manufacturing';
    case ProfessionalServices = 'professional_services';
    case Other = 'other';
}
