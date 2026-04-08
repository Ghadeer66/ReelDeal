<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrderController extends Controller
{
    /**
     * Display a listing of orders for the user.
     */
    public function index()
    {
        $orders = auth()->user()->orders()
            ->with('items.product')
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Orders', [
            'orders' => $orders
        ]);
    }

    /**
     * Display a specific order.
     */
    public function show(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        $order->load(['items.product.media']);

        return Inertia::render('OrderDetails', [
            'order' => $order
        ]);
    }
}
