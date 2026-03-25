<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_accounts', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('industry');
            $table->string('type');
            $table->string('website')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->json('billing_address')->nullable();
            $table->json('shipping_address')->nullable();
            $table->decimal('annual_revenue', 15, 2)->nullable();
            $table->unsignedInteger('employee_count')->nullable();
            $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('owner_team_id')->nullable()->constrained('crm_teams')->nullOnDelete();
            $table->foreignUuid('parent_account_id')->nullable()->constrained('crm_accounts')->nullOnDelete();
            $table->json('tags')->nullable();
            $table->timestamps();

            $table->index(['owner_id', 'owner_team_id']);
            $table->index(['type', 'industry']);
            $table->index('parent_account_id');
            $table->index('name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_accounts');
    }
};
