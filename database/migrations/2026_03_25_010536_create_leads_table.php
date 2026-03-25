<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('leads')) {
            return;
        }

        Schema::create('leads', function (Blueprint $table): void {
            $table->uuid('lead_id')->primary();
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('company')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('lead_source');
            $table->string('status');
            $table->unsignedTinyInteger('score');
            $table->string('rating');
            $table->unsignedBigInteger('campaign_id')->nullable();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->uuid('converted_to_contact_id')->nullable();
            $table->uuid('converted_to_deal_id')->nullable();
            $table->boolean('converted')->default(false);
            $table->timestamp('converted_at')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index(['lead_source', 'status']);
            $table->index(['owner_id', 'converted']);
            $table->index(['rating', 'score']);
            $table->index('campaign_id');
            $table->index('converted_to_contact_id');
            $table->index('converted_to_deal_id');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('leads')) {
            return;
        }

        Schema::dropIfExists('leads');
    }
};
