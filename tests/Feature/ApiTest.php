<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_products_endpoint_is_public_and_paginated(): void
    {
        Product::factory()->count(3)->create();
        Product::factory()->hidden()->create();

        $this->getJson('/api/products')
            ->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure(['data' => [['id', 'name', 'slug', 'price', 'stock', 'in_stock', 'image_url', 'category']], 'links', 'meta']);
    }

    public function test_single_product_endpoint(): void
    {
        $product = Product::factory()->create(['name' => 'Router', 'price' => 3999]);

        $this->getJson("/api/products/{$product->slug}")
            ->assertOk()
            ->assertJsonPath('data.name', 'Router')
            ->assertJsonPath('data.price', 3999);
    }

    public function test_token_is_issued_for_valid_credentials(): void
    {
        $user = User::factory()->create();

        $this->postJson('/api/tokens', ['email' => $user->email, 'password' => 'password'])
            ->assertCreated()
            ->assertJsonStructure(['token', 'token_type', 'user']);

        $this->assertDatabaseCount('personal_access_tokens', 1);
    }

    public function test_token_is_refused_for_wrong_password(): void
    {
        $user = User::factory()->create();

        $this->postJson('/api/tokens', ['email' => $user->email, 'password' => 'wrong'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('email');
    }

    public function test_orders_require_a_token(): void
    {
        $this->getJson('/api/orders')->assertUnauthorized();
    }

    public function test_orders_endpoint_returns_only_own_orders(): void
    {
        $user = User::factory()->create();
        $mine = Order::factory()->for($user)->create();
        $theirs = Order::factory()->create();
        Sanctum::actingAs($user);

        $this->getJson('/api/orders')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.order_number', $mine->order_number);

        $this->getJson("/api/orders/{$theirs->order_number}")->assertForbidden();
    }

    public function test_full_token_flow_with_bearer_header(): void
    {
        $user = User::factory()->create();
        $token = $this->postJson('/api/tokens', ['email' => $user->email, 'password' => 'password'])->json('token');

        $this->withHeader('Authorization', "Bearer {$token}")->getJson('/api/user')
            ->assertOk()
            ->assertJsonPath('email', $user->email);
    }
}
