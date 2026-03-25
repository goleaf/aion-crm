<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_pipelines', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->unsignedSmallInteger('position')->default(1);
            $table->boolean('is_default')->default(false);
            $table->timestamps();

            $table->unique('name');
            $table->index(['position', 'is_default']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_pipelines');
    }
};
