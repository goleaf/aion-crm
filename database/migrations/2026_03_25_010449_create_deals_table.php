<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('crm_deals') || ! Schema::hasTable('crm_pipelines')) {
            return;
        }

        Schema::create('crm_deals', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->foreignUuid('account_id')->constrained('crm_accounts')->cascadeOnDelete();
            $table->foreignUuid('contact_id')->nullable()->constrained('crm_contacts')->nullOnDelete();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('team_id')->nullable()->constrained('crm_teams')->nullOnDelete();
            $table->string('stage');
            $table->unsignedBigInteger('amount_minor');
            $table->string('currency');
            $table->unsignedTinyInteger('probability');
            $table->date('close_date')->nullable();
            $table->string('deal_type');
            $table->foreignUuid('pipeline_id')->constrained('crm_pipelines')->cascadeOnDelete();
            $table->string('lost_reason')->nullable();
            $table->string('source')->nullable();
            $table->timestamps();

            $table->index(['owner_id', 'team_id']);
            $table->index(['pipeline_id', 'stage']);
            $table->index(['close_date', 'stage']);
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('crm_deals')) {
            return;
        }

        Schema::dropIfExists('crm_deals');
    }
};
