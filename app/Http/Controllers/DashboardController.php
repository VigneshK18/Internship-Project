<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        return view('dashboard', [
            'recentOrders' => $user->orders()->withCount('items')->latest()->take(5)->get(),
            'orderCount' => $user->orders()->count(),
            'spent' => (float) $user->orders()->where('status', '!=', 'cancelled')->sum('total'),
        ]);
    }
}
