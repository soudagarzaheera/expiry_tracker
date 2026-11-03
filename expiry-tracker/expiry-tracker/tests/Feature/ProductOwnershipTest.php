<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductOwnershipTest extends TestCase
{
    use RefreshDatabase;

    public function test_normal_user_cannot_view_edit_update_or_delete_another_users_product(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        $payload = [
            'name' => 'Tampered', 'category_id' => $product->category_id, 'batch_number' => 'X-1',
            'quantity' => 2, 'unit' => 'Pieces', 'manufacturing_date' => now()->subDay()->toDateString(),
            'expiry_date' => now()->addMonth()->toDateString(), 'description' => null,
        ];

        $this->actingAs($user)->get(route('products.show', $product))->assertForbidden();
        $this->actingAs($user)->get(route('products.edit', $product))->assertForbidden();
        $this->actingAs($user)->put(route('products.update', $product), $payload)->assertForbidden();
        $this->actingAs($user)->delete(route('products.destroy', $product))->assertForbidden();
        $this->assertDatabaseHas('products', ['id' => $product->id, 'name' => $product->name]);
    }

    public function test_admin_can_access_any_product(): void
    {
        $admin = User::factory()->admin()->create();
        $product = Product::factory()->create();

        $this->actingAs($admin)->get(route('products.show', $product))->assertOk();
    }
}
