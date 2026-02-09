<?php

declare(strict_types=1);

namespace Tests\Feature\Api\V1\Products;

use App\Enums\AccountStatus;
use App\Enums\ProductStatus;
use App\Enums\UserRole;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
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
            'status' => AccountStatus::Active->value,
        ]);

        Sanctum::actingAs($seller);

        $response = $this->postJson('/api/v1/products', [
            'title' => 'Product',
            'condition' => 'New',
            'price' => 10,
            'stock_quantity' => 5,
            'is_digital' => false,
            'allow_returns' => true,
            'return_days' => 30,
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('products', ['title' => 'Product']);
    }

    public function test_create_product_buyer_unauthorized(): void
    {
        $buyer = User::query()->create([
            'email' => 'buyer@example.com',
            'password' => Hash::make('password123'),
            'display_name' => 'Buyer',
            'role' => UserRole::Buyer->value,
            'status' => AccountStatus::Active->value,
        ]);

        Sanctum::actingAs($buyer);

        $response = $this->postJson('/api/v1/products', [
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
            'status' => AccountStatus::Active->value,
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

        $this->getJson('/api/v1/products')->assertOk();
    }

    public function test_get_product_by_id(): void
    {
        $seller = User::query()->create([
            'email' => 'seller@example.com',
            'password' => Hash::make('password123'),
            'display_name' => 'Seller',
            'role' => UserRole::Seller->value,
            'status' => AccountStatus::Active->value,
        ]);

        $product = Product::query()->create([
            'seller_id' => $seller->id,
            'title' => 'Product',
            'condition' => 'New',
            'status' => ProductStatus::Active->value,
            'price' => 10,
            'stock_quantity' => 5,
        ]);

        Sanctum::actingAs($seller);

        $this->getJson('/api/v1/products/'.$product->id)->assertOk();
    }

    public function test_update_product_owner_only(): void
    {
        $seller = User::query()->create([
            'email' => 'seller@example.com',
            'password' => Hash::make('password123'),
            'display_name' => 'Seller',
            'role' => UserRole::Seller->value,
            'status' => AccountStatus::Active->value,
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

        $response = $this->putJson('/api/v1/products/'.$product->id, [
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
            'status' => AccountStatus::Active->value,
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

        $response = $this->deleteJson('/api/v1/products/'.$product->id);

        $response->assertOk();
        $this->assertSoftDeleted('products', ['id' => $product->id]);
    }
}
