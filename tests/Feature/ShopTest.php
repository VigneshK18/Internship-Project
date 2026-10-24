<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShopTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_lists_active_products_only(): void
    {
        $visible = Product::factory()->create(['name' => 'Visible Laptop']);
        Product::factory()->hidden()->create(['name' => 'Secret Prototype']);

        $this->get('/')
            ->assertOk()
            ->assertSee('Visible Laptop')
            ->assertDontSee('Secret Prototype');
    }

    public function test_products_can_be_searched(): void
    {
        Product::factory()->create(['name' => 'Wireless Mouse']);
        Product::factory()->create(['name' => 'Gaming Monitor']);

        $this->get('/?q=mouse')->assertSee('Wireless Mouse')->assertDontSee('Gaming Monitor');
    }

    public function test_products_can_be_filtered_by_category(): void
    {
        $audio = Category::factory()->create(['name' => 'Audio', 'slug' => 'audio']);
        Product::factory()->for($audio)->create(['name' => 'Earbuds']);
        Product::factory()->create(['name' => 'Keyboard']);

        $this->get('/?category=audio')->assertSee('Earbuds')->assertDontSee('Keyboard');
    }

    public function test_products_are_paginated(): void
    {
        Product::factory()->count(config('shop.per_page') + 3)->create();

        $this->get('/')->assertOk()->assertSee('?page=2', false);
    }

    public function test_product_page_shows_details(): void
    {
        $product = Product::factory()->create(['name' => 'Portable SSD', 'price' => 6999]);

        $this->get(route('shop.show', $product))
            ->assertOk()
            ->assertSee('Portable SSD')
            ->assertSee('₹6,999.00');
    }

    public function test_hidden_product_page_returns_404(): void
    {
        $product = Product::factory()->hidden()->create();

        $this->get(route('shop.show', $product))->assertNotFound();
    }
}
