<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_contacts', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('mobile')->nullable();
            $table->string('job_title')->nullable();
            $table->string('department')->nullable();
            $table->foreignUuid('account_id')->nullable()->constrained('crm_accounts')->nullOnDelete();
            $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('owner_team_id')->nullable()->constrained('crm_teams')->nullOnDelete();
            $table->string('lead_source');
            $table->boolean('do_not_contact')->default(false);
            $table->date('birthday')->nullable();
            $table->string('preferred_channel');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['account_id', 'owner_id', 'owner_team_id']);
            $table->index(['last_name', 'first_name']);
            $table->index(['lead_source', 'preferred_channel']);
            $table->index('email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_contacts');
    }
};
