<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_customers_cannot_open_the_admin_panel(): void
    {
        $this->actingAs(User::factory()->create())->get(route('admin.dashboard'))->assertForbidden();
        $this->get(route('admin.products.index'))->assertForbidden();
    }

    public function test_guests_are_sent_to_login(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
    }

    public function test_is_admin_cannot_be_set_through_registration(): void
    {
        $this->post('/register', [
            'name' => 'Sneaky', 'email' => 'sneaky@example.com',
            'password' => 'password', 'password_confirmation' => 'password', 'is_admin' => 1,
        ]);

        $this->assertFalse(User::where('email', 'sneaky@example.com')->first()->is_admin);
    }

    public function test_admin_dashboard_loads(): void
    {
        $this->actingAs(User::factory()->admin()->create())->get(route('admin.dashboard'))->assertOk()->assertSee('Revenue');
    }

    public function test_admin_can_create_a_product_with_an_image(): void
    {
        Storage::fake('uploads');
        $category = Category::factory()->create();

        $this->actingAs(User::factory()->admin()->create())
            ->post(route('admin.products.store'), [
                'name' => 'Laptop Cooling Pad',
                'category_id' => $category->id,
                'description' => 'Five quiet fans.',
                'price' => 1299,
                'stock' => 15,
                'is_active' => 1,
                'image' => UploadedFile::fake()->image('pad.jpg', 800, 600),
            ])
            ->assertRedirect(route('admin.products.index'));

        $product = Product::where('slug', 'laptop-cooling-pad')->sole();
        $this->assertStringStartsWith('uploads/products/', $product->image);
        Storage::disk('uploads')->assertExists(str_replace('uploads/', '', $product->image));
    }

    public function test_product_validation(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->post(route('admin.products.store'), ['name' => '', 'price' => -5])
            ->assertSessionHasErrors(['name', 'price', 'category_id', 'description', 'stock']);
    }

    public function test_admin_can_update_and_delete_products(): void
    {
        $admin = User::factory()->admin()->create();
        $product = Product::factory()->create(['name' => 'Old name']);

        $this->actingAs($admin)->put(route('admin.products.update', $product), [
            'name' => 'New name', 'category_id' => $product->category_id, 'description' => 'x',
            'price' => 100, 'stock' => 1, 'is_active' => 1,
        ])->assertRedirect();
        $this->assertSame('new-name', $product->fresh()->slug);

        $this->delete(route('admin.products.destroy', $product->fresh()))->assertRedirect();
        $this->assertModelMissing($product);
    }

    public function test_category_with_products_cannot_be_deleted(): void
    {
        $product = Product::factory()->create();

        $this->actingAs(User::factory()->admin()->create())
            ->delete(route('admin.categories.destroy', $product->category))
            ->assertSessionHas('error');
        $this->assertModelExists($product->category);
    }

    public function test_admin_can_change_order_status(): void
    {
        $order = Order::factory()->create();

        $this->actingAs(User::factory()->admin()->create())
            ->patch(route('admin.orders.update', $order), ['status' => 'shipped'])
            ->assertSessionHas('success');

        $this->assertSame(OrderStatus::Shipped, $order->fresh()->status);
    }

    public function test_admin_cancelling_an_order_restores_stock(): void
    {
        $product = Product::factory()->create(['stock' => 1]);
        $order = Order::factory()->create();
        $order->items()->create(['product_id' => $product->id, 'product_name' => $product->name, 'unit_price' => 10, 'quantity' => 4, 'line_total' => 40]);

        $this->actingAs(User::factory()->admin()->create())
            ->patch(route('admin.orders.update', $order), ['status' => 'cancelled']);

        $this->assertSame(5, $product->fresh()->stock);
        $this->assertSame(OrderStatus::Cancelled, $order->fresh()->status);
    }
}
