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
        if (empty($searchTerm)) {
            return response()->json(['data' => []]);
        }

        $perPage = $request->input('limit', 5);
        $reels = $this->feedService->searchReels($searchTerm, $perPage);

        return ListingFeedResource::collection($reels);
    }
}
