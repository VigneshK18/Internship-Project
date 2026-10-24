<?php

namespace App\Listeners;

use App\Models\User;
use App\Services\CartService;
use Illuminate\Auth\Events\Login;

/** When a guest logs in, keep whatever they put in the cart before logging in. */
class MergeSessionCart
{
    public function __construct(private CartService $cart)
    {
    }

    public function handle(Login $event): void
    {
        if ($event->user instanceof User) {
            $this->cart->mergeSessionInto($event->user);
        }
    }
}
