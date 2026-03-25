<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activities', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->string('title');
            $table->dateTime('start_at');
            $table->dateTime('end_at');
            $table->boolean('all_day')->default(false);
            $table->string('location')->nullable();
            $table->text('description')->nullable();
            $table->string('status');
            $table->foreignId('organizer_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedInteger('reminder_minutes')->nullable();
            $table->string('recurrence')->default('none');
            $table->timestamps();

            $table->index(['start_at', 'status']);
            $table->index('organizer_id');
            $table->index('type');
            $table->index('end_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
