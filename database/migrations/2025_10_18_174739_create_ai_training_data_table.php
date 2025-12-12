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
        Schema::create('ai_training_data', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('extension');
            $table->text('caption')->nullable();
            $table->string('assets_id')->nullable()->unique();
            $table->foreignUuid('user_id')->nullable()->constrained()->onDelete('SET NULL');
            $table->uuidMorphs('model');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_training_data');
    }
};
