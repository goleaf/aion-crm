<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_user_profiles', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('primary_team_id')->nullable()->constrained('crm_teams')->nullOnDelete();
            $table->string('role');
            $table->string('record_visibility');
            $table->boolean('is_active')->default(true);
            $table->timestamp('deactivated_at')->nullable();
            $table->timestamps();

            $table->unique('user_id');
            $table->index(['primary_team_id', 'is_active']);
            $table->index(['role', 'record_visibility']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_user_profiles');
    }
};
