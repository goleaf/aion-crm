<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('crm_contacts')) {
            return;
        }

        Schema::create('crm_contacts', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('account_id')->nullable()->constrained('crm_accounts')->nullOnDelete();
            $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('team_id')->nullable()->constrained('crm_teams')->nullOnDelete();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('mobile')->nullable();
            $table->string('job_title')->nullable();
            $table->string('department')->nullable();
            $table->string('lead_source')->nullable();
            $table->boolean('do_not_contact')->default(false);
            $table->date('birthday')->nullable();
            $table->string('preferred_channel')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['owner_id', 'team_id']);
            $table->index(['account_id', 'last_name']);
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('crm_contacts')) {
            return;
        }

        Schema::dropIfExists('crm_contacts');
    }
};
