<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SearchController extends Controller
{
    /**
     * Search for products.
     */
    public function index(Request $request)
    {
        $query = $request->get('q', '');
        $categoryId = $request->get('category_id');

        $productsQuery = Product::with(['user', 'media'])
            ->where('status', 'active');

        if (!empty($query)) {
            $productsQuery->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%");
            });
        }

        if (!empty($categoryId)) {
            $productsQuery->where('category_id', $categoryId);
        }

        $products = $productsQuery->orderBy('id', 'desc')->paginate(12);

        // Append bookmark status for authenticated user
        if (auth()->check() && $products->isNotEmpty()) {
            $productIds = $products->pluck('id')->toArray();
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

        return Inertia::render('Search', [
            'products' => $products,
            'query' => $query,
            'categoryId' => $categoryId
        ]);
    }
}
