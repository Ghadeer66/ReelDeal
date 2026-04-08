<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ListingFeedResource;
use Illuminate\Http\Request;

class SaveController extends Controller
{
    /**
     * Display a listing of bookmarked products for the authenticated user.
     */
    public function index(Request $request)
    {
        $perPage = $request->input('limit', 5);
        $savedListings = $request->user()
            ->savedListings()
            ->with(['user', 'category', 'images'])
            ->cursorPaginate($perPage);

        return ListingFeedResource::collection($savedListings);
    }
}
