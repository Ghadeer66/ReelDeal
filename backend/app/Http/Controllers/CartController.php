<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\CartItem;
use App\Http\Requests\UpdateCartItemRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CartController extends Controller
{
    /**
     * Display the user's cart.
     */
    public function index()
    {
        $cartItems = auth()->user()->cartItems()->with('product.media')->get();

        $total = $cartItems->sum(function ($item) {
            return $item->product->price * $item->quantity;
        });

        return Inertia::render('Cart', [
            'cartItems' => $cartItems,
            'total' => $total
        ]);
    }

    /**
     * Add a product to the cart.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'integer|min:1'
        ]);

        $product = Product::findOrFail($request->product_id);
        $user = auth()->user();

        // Check if item already exists in cart
        $cartItem = $user->cartItems()->where('product_id', $product->id)->first();

        if ($cartItem) {
            $cartItem->increment('quantity', $request->get('quantity', 1));
        } else {
            $user->cartItems()->create([
                'product_id' => $product->id,
                'quantity' => $request->get('quantity', 1)
            ]);
        }

        return redirect()->back()->with('success', 'Added to cart');
    }

    /**
     * Update the quantity of a cart item.
     */
    public function update(UpdateCartItemRequest $request, CartItem $cartItem)
    {
        // Ensure user owns this cart item
        if ($cartItem->user_id != auth()->id()) {
            abort(403);
        }

        $cartItem->update(['quantity' => $request->quantity]);

        return redirect()->back()->with('success', 'Cart updated');
    }

    /**
     * Remove the item from the cart.
     */
    public function destroy(CartItem $cartItem)
    {
        if ($cartItem->user_id != auth()->id()) {
            abort(403);
        }

        $cartItem->delete();

        return redirect()->back()->with('success', 'Item removed');
    }

    /**
     * Process checkout (stub).
     */
    public function checkout(Request $request)
    {
        $user = auth()->user();
        $cartItems = $user->cartItems()->with('product')->get();

        if ($cartItems->isEmpty()) {
            return redirect()->back()->with('error', 'Cart is empty');
        }

        $total = $cartItems->sum(function ($item) {
            return $item->product->price * $item->quantity;
        });

        // Ensure we don't have mixed currencies
        $currency = $cartItems->first()->product->currency ?? 'SAR';

        // Create the order stub
        $order = $user->orders()->create([
            'status' => 'pending',
            'total' => $total,
            'currency' => $currency,
            'shipping_address' => json_encode(['address' => 'Sample Address']), // Stub
        ]);

        foreach ($cartItems as $item) {
            $order->items()->create([
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'price_at_purchase' => $item->product->price,
            ]);
            // Clean up cart as we go
            $item->delete();
        }

        return redirect()->route('dashboard')->with('success', 'Order created successfully! (Stub checkout)');
    }
}
