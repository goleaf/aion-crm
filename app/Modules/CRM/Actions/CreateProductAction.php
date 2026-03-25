<?php

namespace App\Modules\CRM\Actions;

use App\Modules\CRM\DataTransferObjects\CreateProductData;
use App\Modules\CRM\Models\Product;
use Illuminate\Support\Str;

/** @final */
class CreateProductAction
{
    public function execute(CreateProductData $data): Product
    {
        return Product::query()->create([
            'name' => $this->normalizeRequiredString($data->name),
            'sku' => Str::upper($this->normalizeRequiredString($data->sku)),
            'description' => $this->normalizeNullableString($data->description),
            'unit_price' => $data->unitPrice,
            'currency' => $data->currency,
            'category' => $this->normalizeRequiredString($data->category),
            'tax_rate' => $data->taxRate,
            'active' => $data->active,
            'recurring' => $data->recurring,
            'billing_frequency' => $data->billingFrequency,
            'cost_price' => $this->normalizeNullableString($data->costPrice),
        ]);
    }

    private function normalizeNullableString(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $normalized = Str::of($value)->trim()->squish()->value();

        return $normalized === '' ? null : $normalized;
    }

    private function normalizeRequiredString(string $value): string
    {
        return Str::of($value)->trim()->squish()->value();
    }
}
