<?php

namespace App\Services;

use App\Models\Listing;
use App\Enums\ListingStatus;
use Illuminate\Support\Facades\Auth;

class ReelFeedService
{
    /**
     * Build personalized feed query with cursor pagination
     */
    public function getFeed($perPage = 5)
    {
        $query = Listing::with(['user', 'category', 'images'])
            ->where('status', ListingStatus::Live->value);

        if ($user = Auth::guard('sanctum')->user()) {
            // Future logic: personalize based on user_interests table
            // This ensures we show listings associated with user interests
            $interestIds = $user->interests()->pluck('category_id')->toArray();
            if (!empty($interestIds)) {
                $query->orderByRaw('CASE WHEN category_id IN (' . implode(',', array_map('intval', $interestIds)) . ') THEN 1 ELSE 2 END');
            }
        }

        // Sort by newest for now as secondary
        $query->latest();

        return $query->cursorPaginate($perPage);
    }

    public function searchReels($searchTerm, $perPage = 5, array $filters = [])
    {
        $query = Listing::with(['user', 'category', 'images'])
            ->where('status', ListingStatus::Live->value);

        if (!empty($searchTerm)) {
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title_en', 'like', "%{$searchTerm}%")
                    ->orWhere('title_ar', 'like', "%{$searchTerm}%")
                    ->orWhere('description_en', 'like', "%{$searchTerm}%");
            });
        }

        // Apply filters
        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (!empty($filters['min_price'])) {
            $query->where('price', '>=', $filters['min_price']);
        }

        if (!empty($filters['max_price'])) {
            $query->where('price', '<=', $filters['max_price']);
        }

        if (!empty($filters['condition'])) {
            if ($filters['condition'] === 'new') {
                $query->where('condition', 'new');
            } else if ($filters['condition'] === 'used') {
                $query->whereIn('condition', ['like_new', 'good', 'fair']);
            }
        }

        if (!empty($filters['location'])) {
            $query->where('location', 'like', "%{$filters['location']}%");
        }

        $query->latest();

        return $query->cursorPaginate($perPage);
    }
}
