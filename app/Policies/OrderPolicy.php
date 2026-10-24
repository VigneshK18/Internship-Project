<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    /** Customers can see their own orders; administrators can see every order. */
    public function view(User $user, Order $order): bool
    {
        return $user->isAdmin() || $order->user_id === $user->id;
    }

    /** Only the customer who placed it can cancel, and only while it is still pending. */
    public function cancel(User $user, Order $order): bool
    {
        return $order->user_id === $user->id && $order->isCancellable();
    }

    /** Only administrators move an order through processing, shipped and delivered. */
    public function updateStatus(User $user, Order $order): bool
    {
        return $user->isAdmin();
    }
}
