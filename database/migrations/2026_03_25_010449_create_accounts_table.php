<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('crm_accounts')) {
            return;
        }

        Schema::create('crm_accounts', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('industry')->nullable();
            $table->string('type')->nullable();
            $table->string('website')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->json('billing_address')->nullable();
            $table->json('shipping_address')->nullable();
            $table->unsignedBigInteger('annual_revenue_minor')->nullable();
            $table->unsignedInteger('employee_count')->nullable();
            $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('team_id')->nullable()->constrained('crm_teams')->nullOnDelete();
            $table->foreignUuid('parent_account_id')->nullable()->constrained('crm_accounts')->nullOnDelete();
            $table->json('tags')->nullable();
            $table->timestamps();

            $table->index(['owner_id', 'team_id']);
            $table->index(['type', 'industry']);
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('crm_accounts')) {
            return;
        }

        Schema::dropIfExists('crm_accounts');
    }
};
