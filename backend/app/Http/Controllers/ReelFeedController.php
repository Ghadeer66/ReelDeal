<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReelFeedController extends Controller
{
    /**
     * Display the initial reel feed.
     */
    public function index(Request $request)
    {
        $products = Product::with(['user', 'media'])
            ->where('status', 'active')
            ->orderBy('id', 'desc')
            ->cursorPaginate(5);

        $this->appendBookmarkStatus($products->items());

        return Inertia::render('ReelFeed', [
            'initialProducts' => $products
        ]);
    }

    /**
     * Load more products for the infinite scroll feed.
     */
    public function loadMore(Request $request)
    {
        $products = Product::with(['user', 'media'])
            ->where('status', 'active')
            ->orderBy('id', 'desc')
            ->cursorPaginate(5);

        $this->appendBookmarkStatus($products->items());

        // Note: Inertia partial reloads can handle cursor pagination elegantly
        // Using response()->json for simpler infinite scroll handling if needed
        return response()->json([
            'products' => $products->items(),
            'next_cursor' => $products->nextCursor()?->encode()
        ]);
    }

    /**
     * Helper to append is_bookmarked status.
     */
    private function appendBookmarkStatus($products)
    {
        if (auth()->check() && count($products) > 0) {
            $productIds = collect($products)->pluck('id')->toArray();
            $bookmarkedIds = auth()->user()->bookmarks()
                ->whereIn('product_id', $productIds)
                ->pluck('product_id')
                ->toArray();

            foreach ($products as $product) {
                $product->is_bookmarked = in_array($product->id, $bookmarkedIds);
            }
        } else {
            foreach ($products as $product) {
                $product->is_bookmarked = false;
            }
        }
    }
}
