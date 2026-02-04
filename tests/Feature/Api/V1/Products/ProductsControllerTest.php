<?php

declare(strict_types=1);

namespace Tests\Feature\Api\V1\Products;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Src\Domain\Shared\Enums\ProductStatus;
use Src\Domain\Shared\Enums\UserRole;
use Src\Infrastructure\Persistence\Eloquent\Models\Product;
use Src\Infrastructure\Persistence\Eloquent\Models\User;
use Tests\TestCase;

class ProductsControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_product_seller_success(): void
    {
        $seller = User::query()->create([
            'email' => 'seller@example.com',
            'password' => Hash::make('password123'),
            'display_name' => 'Seller',
            'role' => UserRole::Seller->value,
        ]);

        Sanctum::actingAs($seller);

        $response = $this->postJson('/api/v1/products/create', [
            'title' => 'Product',
            'condition' => 'New',
            'price' => 10,
            'stock_quantity' => 5,
            'is_digital' => false,
            'allow_returns' => true,
            'return_days' => 30,
        ]);

        $response->assertOk()->assertJsonPath('success', true);
        $this->assertDatabaseHas('products', ['title' => 'Product']);
    }

    public function test_create_product_buyer_unauthorized(): void
    {
        $buyer = User::query()->create([
            'email' => 'buyer@example.com',
            'password' => Hash::make('password123'),
            'display_name' => 'Buyer',
            'role' => UserRole::Buyer->value,
        ]);

        Sanctum::actingAs($buyer);

        $response = $this->postJson('/api/v1/products/create', [
            'title' => 'Product',
            'condition' => 'New',
            'price' => 10,
            'stock_quantity' => 5,
            'is_digital' => false,
            'allow_returns' => true,
            'return_days' => 30,
        ]);

        $response->assertStatus(403);
    }

    public function test_get_my_products(): void
    {
        $seller = User::query()->create([
            'email' => 'seller@example.com',
            'password' => Hash::make('password123'),
            'display_name' => 'Seller',
            'role' => UserRole::Seller->value,
        ]);

        Product::query()->create([
            'seller_id' => $seller->id,
            'title' => 'Product',
            'condition' => 'New',
            'status' => ProductStatus::Draft->value,
            'price' => 10,
            'stock_quantity' => 5,
        ]);

        Sanctum::actingAs($seller);

        $this->getJson('/api/v1/products/get-mine')->assertOk();
    }

    public function test_get_product_by_id(): void
    {
        $seller = User::query()->create([
            'email' => 'seller@example.com',
            'password' => Hash::make('password123'),
            'display_name' => 'Seller',
            'role' => UserRole::Seller->value,
        ]);

        $product = Product::query()->create([
            'seller_id' => $seller->id,
            'title' => 'Product',
            'condition' => 'New',
            'status' => ProductStatus::Active->value,
            'price' => 10,
            'stock_quantity' => 5,
        ]);

        $this->getJson('/api/v1/products/get-by-id/' . $product->id)->assertOk();
    }

    public function test_update_product_owner_only(): void
    {
        $seller = User::query()->create([
            'email' => 'seller@example.com',
            'password' => Hash::make('password123'),
            'display_name' => 'Seller',
            'role' => UserRole::Seller->value,
        ]);

        $product = Product::query()->create([
            'seller_id' => $seller->id,
            'title' => 'Product',
            'condition' => 'New',
            'status' => ProductStatus::Draft->value,
            'price' => 10,
            'stock_quantity' => 5,
        ]);

        Sanctum::actingAs($seller);

        $response = $this->putJson('/api/v1/products/update/' . $product->id, [
            'title' => 'Updated',
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('products', ['id' => $product->id, 'title' => 'Updated']);
    }

    public function test_delete_product_soft_delete(): void
    {
        $seller = User::query()->create([
            'email' => 'seller@example.com',
            'password' => Hash::make('password123'),
            'display_name' => 'Seller',
            'role' => UserRole::Seller->value,
        ]);

        $product = Product::query()->create([
            'seller_id' => $seller->id,
            'title' => 'Product',
            'condition' => 'New',
            'status' => ProductStatus::Draft->value,
            'price' => 10,
            'stock_quantity' => 5,
        ]);

        Sanctum::actingAs($seller);

        $response = $this->deleteJson('/api/v1/products/delete/' . $product->id);

        $response->assertOk();
        $this->assertDatabaseHas('products', ['id' => $product->id, 'is_deleted' => true]);
    }
}
<?php

namespace Tests\Feature\Api\V1\Products;

use Tests\TestCase;

class ProductsControllerTest extends TestCase
{
}
