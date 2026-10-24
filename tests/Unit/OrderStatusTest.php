<?php

namespace Tests\Unit;

use App\Enums\OrderStatus;
use PHPUnit\Framework\TestCase;

class OrderStatusTest extends TestCase
{
    public function test_labels_and_options(): void
    {
        $this->assertSame('Shipped', OrderStatus::Shipped->label());
        $this->assertSame(['pending', 'processing', 'shipped', 'delivered', 'cancelled'], array_keys(OrderStatus::options()));
        $this->assertStringContainsString('green', OrderStatus::Delivered->badgeClasses());
    }
}
