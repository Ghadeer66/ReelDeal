<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class BookmarkController extends Controller
{
    /**
     * Display a listing of bookmarked products.
     */
    public function index()
    {
        $bookmarks = auth()->user()->bookmarks()->with('product.media')->get();
        // Extract products from bookmarks for easier frontend handling
        $products = $bookmarks->pluck('product');

        foreach ($products as $product) {
            $product->is_bookmarked = true;
        }

        return inertia('Bookmarks', [
            'products' => $products
        ]);
    }

    /**
     * Toggle bookmark status for a product.
     */
    public function toggle(Product $product)
    {
        $user = auth()->user();
        $bookmark = $user->bookmarks()->where('product_id', $product->id)->first();

        if ($bookmark) {
            $bookmark->delete();
            $status = false;
        } else {
            $user->bookmarks()->create(['product_id' => $product->id]);
            $status = true;
        }

        return response()->json([
            'success' => true,
            'is_bookmarked' => $status
        ]);
    }
}
