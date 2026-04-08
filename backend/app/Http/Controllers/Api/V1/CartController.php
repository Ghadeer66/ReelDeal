<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $cartItems = $user->cartItems()->with(['listing.user', 'listing.category', 'listing.images'])->get();

        $total = $cartItems->sum(function ($item) {
            return $item->listing->price * $item->quantity;
        });

        return response()->json([
            'items' => $cartItems,
            'total' => $total,
            'count' => $cartItems->sum('quantity')
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'listing_id' => 'required|exists:listings,id',
            'quantity' => 'integer|min:1'
        ]);

        $user = $request->user();
        $cartItem = $user->cartItems()->where('listing_id', $request->listing_id)->first();

        if ($cartItem) {
            $cartItem->increment('quantity', $request->get('quantity', 1));
        } else {
            $cartItem = $user->cartItems()->create([
                'listing_id' => $request->listing_id,
                'quantity' => $request->get('quantity', 1)
            ]);
        }

        return response()->json(['message' => 'Added to cart', 'item' => $cartItem]);
    }

    public function update(Request $request, $id)
    {
        $request->validate(['quantity' => 'required|integer|min:1']);

        $cartItem = $request->user()->cartItems()->findOrFail($id);
        $cartItem->update(['quantity' => $request->quantity]);

        return response()->json(['message' => 'Cart updated', 'item' => $cartItem]);
    }

    public function destroy(Request $request, $id)
    {
        $cartItem = $request->user()->cartItems()->findOrFail($id);
        $cartItem->delete();

        return response()->json(['message' => 'Item removed']);
    }
}
