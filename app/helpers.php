<?php

if (! function_exists('money')) {
    /** Format an amount in the shop currency, e.g. money(1499) -> "₹1,499.00". */
    function money(float|int|string|null $amount): string
    {
        return config('shop.currency').number_format((float) $amount, 2);
    }
}
