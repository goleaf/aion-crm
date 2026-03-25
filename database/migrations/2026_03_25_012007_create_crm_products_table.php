<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_products', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('sku')->unique();
            $table->text('description')->nullable();
            $table->decimal('unit_price', 12, 2);
            $table->string('currency', 3);
            $table->string('category');
            $table->decimal('tax_rate', 5, 2)->default('0.00');
            $table->boolean('active')->default(true);
            $table->boolean('recurring')->default(false);
            $table->string('billing_frequency');
            $table->decimal('cost_price', 12, 2)->nullable();
            $table->timestamps();

            $table->index('active');
            $table->index('category');
            $table->index('currency');
            $table->index('billing_frequency');
            $table->index(['active', 'category']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_products');
    }
};
