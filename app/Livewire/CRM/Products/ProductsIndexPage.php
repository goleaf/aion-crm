<?php

namespace App\Livewire\CRM\Products;

use App\Http\Web\CRM\Products\Requests\UpsertProductRequest;
use App\Modules\CRM\Actions\CreateProductAction;
use App\Modules\CRM\Actions\UpdateProductAction;
use App\Modules\CRM\DataTransferObjects\CreateProductData;
use App\Modules\CRM\DataTransferObjects\UpdateProductData;
use App\Modules\CRM\Enums\BillingFrequencyEnum;
use App\Modules\CRM\Foundation\Enums\CurrencyCodeEnum;
use App\Modules\CRM\Models\Product;
use App\Modules\Shared\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Livewire\Component;

class ProductsIndexPage extends Component
{
    use AuthorizesRequests;

    public string $search = '';

    public string $activeFilter = '';

    public string $categoryFilter = '';

    public string $recurringFilter = '';

    public string $billingFrequencyFilter = '';

    public ?string $editingProductId = null;

    /** @var array<string, mixed> */
    public array $form = [];

    public ?string $statusMessage = null;

    public function mount(): void
    {
        $this->authorize('viewAny', Product::class);
        $this->resetForm();
    }

    public function saveProduct(): void
    {
        $product = $this->currentProduct();

        if ($product instanceof Product) {
            $this->authorize('update', $product);
        } else {
            $this->authorize('create', Product::class);
        }

        $this->form = $this->normalizedForm($this->form);

        $validated = $this->validate($this->rules());
        $payload = $validated['form'];
        $billingFrequency = BillingFrequencyEnum::from((string) data_get($payload, 'billing_frequency'));

        if ($product instanceof Product) {
            resolve(UpdateProductAction::class)->execute(
                new UpdateProductData(
                    product: $product,
                    name: (string) data_get($payload, 'name', ''),
                    sku: (string) data_get($payload, 'sku', ''),
                    description: data_get($payload, 'description'),
                    unitPrice: (string) data_get($payload, 'unit_price', '0.00'),
                    currency: CurrencyCodeEnum::from((string) data_get($payload, 'currency')),
                    category: (string) data_get($payload, 'category', ''),
                    taxRate: (string) data_get($payload, 'tax_rate', '0.00'),
                    active: (bool) data_get($payload, 'active', false),
                    recurring: (bool) data_get($payload, 'recurring', false),
                    billingFrequency: $billingFrequency,
                    costPrice: data_get($payload, 'cost_price'),
                ),
            );

            $this->statusMessage = 'Product updated.';
        } else {
            resolve(CreateProductAction::class)->execute(
                new CreateProductData(
                    name: (string) data_get($payload, 'name', ''),
                    sku: (string) data_get($payload, 'sku', ''),
                    description: data_get($payload, 'description'),
                    unitPrice: (string) data_get($payload, 'unit_price', '0.00'),
                    currency: CurrencyCodeEnum::from((string) data_get($payload, 'currency')),
                    category: (string) data_get($payload, 'category', ''),
                    taxRate: (string) data_get($payload, 'tax_rate', '0.00'),
                    active: (bool) data_get($payload, 'active', false),
                    recurring: (bool) data_get($payload, 'recurring', false),
                    billingFrequency: $billingFrequency,
                    costPrice: data_get($payload, 'cost_price'),
                ),
            );

            $this->statusMessage = 'Product created.';
        }

        $this->resetForm();
    }

    public function editProduct(string $productId): void
    {
        $product = $this->findEditableProduct($productId);

        $this->authorize('update', $product);

        $this->editingProductId = $product->getKey();
        $this->form = [
            'name' => $product->name,
            'sku' => $product->sku,
            'description' => $product->description,
            'unit_price' => $product->unit_price,
            'currency' => $product->currency->value,
            'category' => $product->category,
            'tax_rate' => $product->tax_rate,
            'active' => $product->active,
            'recurring' => $product->recurring,
            'billing_frequency' => $product->billing_frequency->value,
            'cost_price' => $product->cost_price,
        ];

        $this->statusMessage = null;
    }

    public function cancelEditing(): void
    {
        $this->resetForm();
    }

    public function render(): View
    {
        $actor = auth()->user();
        assert($actor instanceof User);

        return view('livewire.crm.products.products-index-page', [
            'products' => Product::query()
                ->indexPayload()
                ->searchTerm($this->search)
                ->withActiveStatus($this->activeFilter)
                ->inCategory($this->categoryFilter)
                ->recurringStatus($this->recurringFilter)
                ->withBillingFrequency($this->billingFrequencyFilter)
                ->orderBy('name')
                ->get(),
            'billingFrequencyOptions' => BillingFrequencyEnum::cases(),
            'currencyOptions' => CurrencyCodeEnum::cases(),
            'categoryOptions' => $this->categoryOptions(),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        $rules = [];

        foreach (UpsertProductRequest::rulesFor($this->currentProduct()) as $key => $value) {
            $rules["form.{$key}"] = $value;
        }

        return $rules;
    }

    private function currentProduct(): ?Product
    {
        if ($this->editingProductId === null) {
            return null;
        }

        return Product::query()->find($this->editingProductId);
    }

    private function findEditableProduct(string $productId): Product
    {
        return Product::query()->findOrFail($productId);
    }

    /**
     * @return Collection<int, string>
     */
    private function categoryOptions(): Collection
    {
        return Product::query()
            ->select(['category'])
            ->orderBy('category')
            ->distinct()
            ->pluck('category');
    }

    /**
     * @param array<string, mixed> $form
     * @return array<string, mixed>
     */
    private function normalizedForm(array $form): array
    {
        return [
            'name' => $this->normalizeNullableString(data_get($form, 'name')) ?? '',
            'sku' => Str::upper($this->normalizeNullableString(data_get($form, 'sku')) ?? ''),
            'description' => $this->normalizeNullableString(data_get($form, 'description')),
            'unit_price' => $this->normalizeNullableString(data_get($form, 'unit_price')) ?? '0.00',
            'currency' => data_get($form, 'currency', CurrencyCodeEnum::EUR->value),
            'category' => $this->normalizeNullableString(data_get($form, 'category')) ?? '',
            'tax_rate' => $this->normalizeNullableString(data_get($form, 'tax_rate')) ?? '0.00',
            'active' => (bool) data_get($form, 'active', true),
            'recurring' => (bool) data_get($form, 'recurring', false),
            'billing_frequency' => data_get($form, 'billing_frequency', BillingFrequencyEnum::OneTime->value),
            'cost_price' => $this->normalizeNullableString(data_get($form, 'cost_price')),
        ];
    }

    private function resetForm(): void
    {
        $this->editingProductId = null;
        $this->form = [
            'name' => '',
            'sku' => '',
            'description' => null,
            'unit_price' => '0.00',
            'currency' => CurrencyCodeEnum::EUR->value,
            'category' => '',
            'tax_rate' => '0.00',
            'active' => true,
            'recurring' => false,
            'billing_frequency' => BillingFrequencyEnum::OneTime->value,
            'cost_price' => null,
        ];
    }

    private function normalizeNullableString(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $normalized = Str::of($value)->trim()->squish()->value();

        return $normalized === '' ? null : $normalized;
    }
}
