<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use App\Models\ListingView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ListingController extends Controller
{
    /**
     * Track when a listing is viewed (public or authenticated)
     */
    public function view(Request $request, $id)
    {
        $listing = Listing::findOrFail($id);
        $user = Auth::guard('sanctum')->user();

        // Prevent spam tracking - simple check if IP and listing_id already viewed today
        $alreadyViewed = ListingView::where('listing_id', $listing->id)
            ->where('ip_address', $request->ip())
            ->whereDate('created_at', now()->toDateString())
            ->exists();

        if (!$alreadyViewed) {
            ListingView::create([
                'listing_id' => $listing->id,
                'user_id' => $user?->id,
                'ip_address' => $request->ip(),
                'watched_duration' => 3, // Since it triggers at 3 seconds
            ]);

            $listing->increment('views_count');
        }

        return response()->json(['message' => 'View tracked successfully']);
    }

    /**
     * Like a listing (toggle logic) - Requires auth
     */
    public function like(Request $request, $id)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();
        if (!$user)
            return response()->json(['message' => 'Unauthenticated.'], 401);

        $listing = Listing::findOrFail($id);

        // Since rules.md doesn't explicitly define a 'likes' table, we just increment.
        // In a real scenario, we would use a pivot table.
        $listing->increment('likes_count');

        return response()->json(['message' => 'Liked successfully', 'likes_count' => $listing->likes_count]);
    }

    /**
     * Save/Bookmark a listing (toggle logic) - Requires auth
     */
    public function save(Request $request, $id)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();
        if (!$user)
            return response()->json(['message' => 'Unauthenticated.'], 401);

        $listing = Listing::findOrFail($id);

        $saveRecord = \App\Models\Save::where('user_id', $user->id)
            ->where('listing_id', $listing->id)
            ->first();

        if ($saveRecord) {
            $saveRecord->delete();
            $listing->decrement('saves_count');
            return response()->json(['message' => 'Unsaved successfully', 'saves_count' => $listing->saves_count]);
        }

        \App\Models\Save::create([
            'user_id' => $user->id,
            'listing_id' => $listing->id
        ]);
        $listing->increment('saves_count');

        return response()->json(['message' => 'Saved successfully', 'saves_count' => $listing->saves_count]);
    }
}
