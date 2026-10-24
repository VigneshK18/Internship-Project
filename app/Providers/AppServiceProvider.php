<?php

namespace App\Providers;

use App\Models\User;
use App\Services\CartService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // one cart instance per request
        $this->app->scoped(CartService::class);
    }

    public function boot(): void
    {
        // Administrator role: used by the `can:admin` middleware on the /admin routes
        Gate::define('admin', fn (User $user) => $user->isAdmin());

        // cart badge in the navigation bar
        View::composer('layouts.navigation', function ($view) {
            $view->with('cartCount', app(CartService::class)->count());
        });
    }
}
