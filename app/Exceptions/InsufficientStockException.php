<?php

namespace App\Exceptions;

use RuntimeException;

class InsufficientStockException extends RuntimeException
{
    public static function for(string $productName, int $available): self
    {
        return new self("Only {$available} unit(s) of \"{$productName}\" are left in stock. Please update your cart.");
    }
}
