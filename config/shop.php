<?php

return [
    'name' => env('APP_NAME', 'TechKart'),
    'currency' => '₹',
    // orders at or above this subtotal ship free; below it a flat fee is charged
    'free_shipping_threshold' => 999,
    'shipping_fee' => 49,
    // pending orders older than this are cancelled by the scheduled orders:cancel-stale command
    'stale_order_hours' => 48,
    'per_page' => 9,
];
