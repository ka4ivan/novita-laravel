<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ai_trainings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('status');
            $table->string('type');
            $table->string('base_model')->nullable();
            $table->string('model_name')->nullable();
            $table->jsonb('options')->nullable();
            $table->json('extra')->nullable();
            $table->string('task_id')->nullable();
            $table->foreignUuid('user_id')->nullable()->constrained()->onDelete('SET NULL');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_trainings');
    }
};
