<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ListingFeedResource;
use App\Services\ReelFeedService;
use Illuminate\Http\Request;

class FeedController extends Controller
{
    protected $feedService;

    public function __construct(ReelFeedService $feedService)
    {
        $this->feedService = $feedService;
    }

    public function index(Request $request)
    {
        $perPage = $request->input('limit', 5);
        $reels = $this->feedService->getFeed($perPage);

        return ListingFeedResource::collection($reels);
    }

    public function search(Request $request)
    {
        $searchTerm = $request->input('q', '');
        $perPage = $request->input('limit', 5);
        $filters = [
            'category_id' => $request->input('category_id'),
            'min_price' => $request->input('min_price'),
            'max_price' => $request->input('max_price'),
            'condition' => $request->input('condition'),
            'location' => $request->input('location'),
        ];

        // If no search term and no filters, returned empty or trending
        if (empty($searchTerm) && empty(array_filter($filters))) {
            return response()->json(['data' => []]);
        }

        $reels = $this->feedService->searchReels($searchTerm, $perPage, $filters);

        return ListingFeedResource::collection($reels);
    }
}
