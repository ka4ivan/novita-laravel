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
        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('gateway')->nullable();
            $table->string('status');
            $table->decimal('amount', 8, 2);
            $table->string('comment');
            $table->string('currency_code');
            $table->json('extra');
            $table->string('extern_id')->nullable();
            $table->foreignUuid('user_id')->nullable()->constrained()->onDelete('NO ACTION');
            $table->uuidMorphs('model');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
