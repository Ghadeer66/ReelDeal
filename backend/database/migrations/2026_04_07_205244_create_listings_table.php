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
        Schema::create('listings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('category_id')->constrained('categories')->cascadeOnDelete();
            $table->string('title_en');
            $table->string('title_ar');
            $table->text('description_en');
            $table->text('description_ar');
            $table->bigInteger('price');
            $table->char('currency', 3)->default('SAR');
            $table->enum('condition', ['new', 'like_new', 'good', 'fair']);
            $table->enum('seller_type', ['individual', 'merchant']);
            $table->enum('status', ['draft', 'pending_review', 'approved', 'live', 'paused', 'rejected', 'sold'])->default('draft');
            $table->string('rejection_reason')->nullable();
            $table->enum('media_type', ['video', 'image_with_audio']);
            $table->string('video_url')->nullable();
            $table->string('thumbnail_url')->nullable();
            $table->bigInteger('views_count')->default(0);
            $table->bigInteger('likes_count')->default(0);
            $table->bigInteger('saves_count')->default(0);
            $table->bigInteger('market_price_min')->nullable();
            $table->bigInteger('market_price_max')->nullable();
            $table->enum('price_flag', ['normal', 'high', 'low', 'suspicious'])->default('normal');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('listings');
    }
};
