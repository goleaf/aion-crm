<?php

namespace App\Modules\CRM\DataTransferObjects;

use App\Modules\CRM\Enums\BillingFrequencyEnum;
use App\Modules\CRM\Foundation\Enums\CurrencyCodeEnum;
use App\Modules\CRM\Models\Product;

final readonly class UpdateProductData
{
    public function __construct(
        public Product $product,
        public string $name,
        public string $sku,
        public ?string $description,
        public string $unitPrice,
        public CurrencyCodeEnum $currency,
        public string $category,
        public string $taxRate,
        public bool $active,
        public bool $recurring,
        public BillingFrequencyEnum $billingFrequency,
        public ?string $costPrice,
    ) {}
}
