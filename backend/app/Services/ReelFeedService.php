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

    public function searchReels($searchTerm, $perPage = 5)
    {
        $query = Listing::with(['user', 'category', 'images'])
            ->where('status', ListingStatus::Live->value)
            ->where(function ($q) use ($searchTerm) {
                $q->where('title_en', 'like', "%{$searchTerm}%")
                    ->orWhere('title_ar', 'like', "%{$searchTerm}%")
                    ->orWhere('description_en', 'like', "%{$searchTerm}%");
            })
            ->latest();

        return $query->cursorPaginate($perPage);
    }
}
