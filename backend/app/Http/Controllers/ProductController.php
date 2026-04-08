<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductMedia;
use App\Http\Requests\StoreProductRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('ProductUpload', [
            'categories' => \App\Models\Category::orderBy('name_en')->get()
        ]);
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(StoreProductRequest $request)
    {
        $validated = $request->validated();

        $product = Product::create([
            'user_id' => auth()->id(),
            'category_id' => $validated['category_id'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? '',
            'price' => $validated['price'],
            'currency' => $validated['currency'] ?? 'SAR',
            'condition' => $validated['condition'],
            'status' => 'active',
        ]);

        // Handle media uploads
        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $index => $file) {
                $path = $file->store('products', 'public');
                $isImage = str_starts_with($file->getMimeType(), 'image/');

                ProductMedia::create([
                    'product_id' => $product->id,
                    'type' => $isImage ? 'image' : 'video',
                    'path' => $path,
                    'mime_type' => $file->getMimeType(),
                    'size_bytes' => $file->getSize(),
                    'sort_order' => $index,
                    'is_primary' => $index === 0,
                ]);
            }
        }

        return redirect()->route('dashboard')->with('success', 'Product uploaded successfully!');
    }

    /**
     * Display the specified product.
     */
    public function show(Product $product)
    {
        $product->load(['user', 'media', 'category']);
        $product->increment('views_count');

        if (auth()->check()) {
            $product->is_bookmarked = auth()->user()->bookmarks()->where('product_id', $product->id)->exists();
        } else {
            $product->is_bookmarked = false;
        }

        return Inertia::render('ProductDetails', [
            'product' => $product
        ]);
    }

    /**
     * Increment likes count via API endpoint.
     */
    public function like(Product $product)
    {
        $product->increment('likes_count');
        return response()->json(['success' => true, 'likes_count' => $product->likes_count]);
    }
}
