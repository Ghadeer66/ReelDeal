<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\FeedController;
use App\Http\Controllers\Api\V1\ListingController;
use App\Http\Controllers\Api\V1\AIReelController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\CartController;
use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\SellerController;
use App\Http\Controllers\Api\V1\SaveController;
use App\Http\Controllers\Api\V1\ReviewController;
use App\Http\Controllers\Api\V1\WebhookController;

Route::prefix('v1')->group(function () {

    // Auth
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);

        Route::middleware('auth:sanctum')->group(function () {
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::get('/me', [AuthController::class, 'me']);
            Route::post('/refresh', [AuthController::class, 'refresh']);
        });
    });

    // Feed (public + authenticated)
    // We omit strict auth middleware here, controller can handle optional user
    Route::get('/feed', [FeedController::class, 'index']);
    Route::get('/feed/search', [FeedController::class, 'search']);

    // Categories
    Route::get('/categories', [CategoryController::class, 'index']);

    // Single listing detail (public)
    Route::get('/listings/{id}', [ListingController::class, 'show']);

    // Track view (public or auth)
    Route::post('/listings/{id}/view', [ListingController::class, 'view']);

    // Stripe Webhooks (public)
    Route::post('/webhooks/stripe', [WebhookController::class, 'stripe']);

    // N8N Webhooks (internal/secret)
    Route::prefix('webhooks/n8n')->group(function () {
        Route::post('/listing-approved', [WebhookController::class, 'n8nApproved']);
        Route::post('/listing-rejected', [WebhookController::class, 'n8nRejected']);
        Route::post('/reel-ready', [WebhookController::class, 'n8nReelReady']);
    });

    // Public reviews
    Route::get('/users/{id}/reviews', [ReviewController::class, 'userReviews']);

    // Protected Routes
    Route::middleware('auth:sanctum')->group(function () {

        // Listings
        Route::post('/listings', [ListingController::class, 'store']);
        Route::patch('/listings/{id}', [ListingController::class, 'update']);
        Route::delete('/listings/{id}', [ListingController::class, 'destroy']);
        Route::post('/listings/{id}/like', [ListingController::class, 'like']);
        Route::post('/listings/{id}/save', [ListingController::class, 'save']);

        // AI Reel Generation
        Route::post('/listings/{id}/generate-script', [AIReelController::class, 'generateScript']);
        Route::post('/listings/{id}/generate-reel', [AIReelController::class, 'generateReel']);

        // Cart
        Route::get('/cart', [CartController::class, 'index']);
        Route::post('/cart', [CartController::class, 'store']);
        Route::delete('/cart/{listingId}', [CartController::class, 'destroy']);

        // Orders
        Route::post('/orders', [OrderController::class, 'store']);
        Route::get('/orders', [OrderController::class, 'index']);
        Route::get('/orders/{id}', [OrderController::class, 'show']);
        Route::post('/orders/{id}/confirm-delivery', [OrderController::class, 'confirmDelivery']);
        Route::post('/orders/{id}/dispute', [OrderController::class, 'dispute']);

        // Seller
        Route::prefix('seller')->group(function () {
            Route::get('/listings', [SellerController::class, 'listings']);
            Route::get('/orders', [SellerController::class, 'orders']);
            Route::get('/analytics', [SellerController::class, 'analytics']);
        });

        // Saves
        Route::get('/saves', [SaveController::class, 'index']);

        // Reviews
        Route::post('/reviews', [ReviewController::class, 'store']);
    });

});
