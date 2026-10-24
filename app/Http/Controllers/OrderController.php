<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $orders = $request->user()->orders()->withCount('items')->latest()->paginate(10);

        return view('orders.index', compact('orders'));
    }

    public function show(Order $order): View
    {
        Gate::authorize('view', $order);
        $order->load('items.product');

        return view('orders.show', compact('order'));
    }

    public function cancel(Order $order, OrderService $orders): RedirectResponse
    {
        Gate::authorize('cancel', $order);
        $orders->cancel($order->load('items'));

        return redirect()->route('orders.show', $order)->with('success', 'Your order has been cancelled.');
    }
}
