<?php

namespace App\Modules\CRM\Models;

use App\Modules\CRM\Enums\BillingFrequencyEnum;
use App\Modules\CRM\Foundation\Enums\CurrencyCodeEnum;
use Database\Factories\Modules\CRM\Models\ProductFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory;
    use HasUuids;

    protected $table = 'crm_products';

    public $incrementing = false;

    protected $keyType = 'string';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'sku',
        'description',
        'unit_price',
        'currency',
        'category',
        'tax_rate',
        'active',
        'recurring',
        'billing_frequency',
        'cost_price',
    ];

    protected static function newFactory(): ProductFactory
    {
        return ProductFactory::new();
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'currency' => CurrencyCodeEnum::class,
            'tax_rate' => 'decimal:2',
            'active' => 'boolean',
            'recurring' => 'boolean',
            'billing_frequency' => BillingFrequencyEnum::class,
            'cost_price' => 'decimal:2',
        ];
    }

    /**
     * @return list<string>
     */
    public static function listColumns(): array
    {
        return [
            'id',
            'name',
            'sku',
            'description',
            'unit_price',
            'currency',
            'category',
            'tax_rate',
            'active',
            'recurring',
            'billing_frequency',
            'cost_price',
            'created_at',
            'updated_at',
        ];
    }

    public function scopeIndexPayload(Builder $query): void
    {
        $query->select(self::listColumns());
    }

    public function scopeSearchTerm(Builder $query, ?string $term): void
    {
        if (blank($term)) {
            return;
        }

        $query->where(function (Builder $builder) use ($term): void {
            $builder
                ->where('name', 'like', '%'.$term.'%')
                ->orWhere('sku', 'like', '%'.$term.'%')
                ->orWhere('description', 'like', '%'.$term.'%')
                ->orWhere('category', 'like', '%'.$term.'%');
        });
    }

    public function scopeWithActiveStatus(Builder $query, ?string $status): void
    {
        if ($status === 'active') {
            $query->where('active', true);

            return;
        }

        if ($status === 'inactive') {
            $query->where('active', false);
        }
    }

    public function scopeInCategory(Builder $query, ?string $category): void
    {
        if (blank($category)) {
            return;
        }

        $query->where('category', $category);
    }

    public function scopeRecurringStatus(Builder $query, ?string $status): void
    {
        if ($status === 'recurring') {
            $query->where('recurring', true);

            return;
        }

        if ($status === 'one-time') {
            $query->where('recurring', false);
        }
    }

    public function scopeWithBillingFrequency(Builder $query, ?string $frequency): void
    {
        if (blank($frequency)) {
            return;
        }

        $query->where('billing_frequency', $frequency);
    }

    protected function margin(): Attribute
    {
        return Attribute::make(
            get: fn (): string => $this->decimalFromMinorUnits(
                $this->minorUnitsFromDecimal($this->unit_price) - $this->minorUnitsFromDecimal($this->cost_price),
            ),
        );
    }

    private function minorUnitsFromDecimal(?string $decimalAmount): int
    {
        if ($decimalAmount === null || trim($decimalAmount) === '') {
            return 0;
        }

        $normalized = trim($decimalAmount);
        $negative = str_starts_with($normalized, '-');
        $normalized = ltrim($normalized, '-');
        [$wholePart, $fractionPart] = array_pad(explode('.', $normalized, 2), 2, '0');
        $fractionPart = str_pad(substr($fractionPart, 0, 2), 2, '0');
        $amount = ((int) $wholePart * 100) + (int) $fractionPart;

        return $negative ? $amount * -1 : $amount;
    }

    private function decimalFromMinorUnits(int $minorUnits): string
    {
        $negative = $minorUnits < 0 ? '-' : '';
        $absoluteMinorUnits = abs($minorUnits);
        $wholePart = intdiv($absoluteMinorUnits, 100);
        $fractionPart = str_pad((string) ($absoluteMinorUnits % 100), 2, '0', STR_PAD_LEFT);

        return "{$negative}{$wholePart}.{$fractionPart}";
    }
}
