<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('buyer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('seller_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('listing_id')->constrained('listings')->cascadeOnDelete();
            $table->enum('status', ['pending', 'paid', 'shipped', 'delivered', 'disputed', 'refunded', 'completed']);
            $table->bigInteger('amount');
            $table->char('currency', 3)->default('SAR');
            $table->string('stripe_payment_intent_id')->unique();
            $table->string('stripe_transfer_id')->nullable();
            $table->timestamp('escrow_released_at')->nullable();
            $table->timestamp('delivery_confirmed_at')->nullable();
            $table->timestamp('dispute_opened_at')->nullable();
            $table->json('shipping_address');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
