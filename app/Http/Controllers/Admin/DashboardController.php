<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'stats' => [
                'Revenue' => config('shop.currency').number_format((float) Order::where('status', '!=', OrderStatus::Cancelled)->sum('total'), 2),
                'Orders' => Order::count(),
                'Pending orders' => Order::where('status', OrderStatus::Pending)->count(),
                'Products' => Product::count(),
                'Customers' => User::where('is_admin', false)->count(),
            ],
            'recentOrders' => Order::with('user')->latest()->take(6)->get(),
            'lowStock' => Product::where('stock', '<', 5)->orderBy('stock')->take(6)->get(),
        ]);
    }
}
