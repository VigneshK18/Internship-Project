<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status');
        $orders = Order::with('user')
            ->withCount('items')
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($request->query('q'), fn ($q, $term) => $q->where(function ($q) use ($term) {
                $q->where('order_number', 'like', "%{$term}%")->orWhere('customer_name', 'like', "%{$term}%");
            }))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.orders.index', compact('orders', 'status'));
    }

    public function show(Order $order): View
    {
        $order->load('items.product', 'user');

        return view('admin.orders.show', compact('order'));
    }

    public function update(Request $request, Order $order, OrderService $orders): RedirectResponse
    {
        Gate::authorize('updateStatus', $order);
        $data = $request->validate(['status' => ['required', Rule::enum(OrderStatus::class)]]);
        $new = OrderStatus::from($data['status']);

        if ($order->status === OrderStatus::Cancelled) {
            return back()->with('error', 'A cancelled order cannot be changed.');
        }

        if ($new === OrderStatus::Cancelled) {
            $orders->cancel($order->load('items'));   // also puts the items back into stock
        } else {
            $order->update(['status' => $new]);
        }

        return back()->with('success', "Order {$order->order_number} marked as {$new->label()}.");
    }
}
