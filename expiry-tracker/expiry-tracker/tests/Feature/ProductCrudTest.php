<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_update_and_delete_own_product(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $payload = [
            'name' => 'Milk Carton', 'category_id' => $category->id, 'batch_number' => 'MILK-22',
            'quantity' => 5, 'unit' => 'Cartons', 'manufacturing_date' => now()->subDay()->toDateString(),
            'expiry_date' => now()->addDays(10)->toDateString(), 'description' => 'Keep refrigerated',
        ];

        $this->actingAs($user)->post(route('products.store'), $payload)->assertRedirect(route('products.index'));
        $product = Product::firstOrFail();
        $this->assertSame($user->id, $product->user_id);

        $payload['name'] = 'Updated Milk';
        $this->actingAs($user)->put(route('products.update', $product), $payload)->assertRedirect(route('products.show', $product));
        $this->assertDatabaseHas('products', ['id' => $product->id, 'name' => 'Updated Milk']);

        $this->actingAs($user)->delete(route('products.destroy', $product))->assertRedirect(route('products.index'));
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_product_validation_rejects_expiry_before_manufacturing(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        $this->actingAs($user)->from(route('products.create'))->post(route('products.store'), [
            'name' => 'Invalid Product', 'category_id' => $category->id, 'batch_number' => 'BAD-1',
            'quantity' => 1, 'unit' => 'Piece', 'manufacturing_date' => now()->toDateString(),
            'expiry_date' => now()->subDay()->toDateString(),
        ])->assertSessionHasErrors('expiry_date');
    }

    public function test_expired_product_can_be_marked_for_disposal(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['user_id' => $user->id, 'expiry_date' => now()->subDay()]);

        $this->actingAs($user)->patch(route('products.dispose', $product))->assertSessionHasNoErrors();
        $this->assertDatabaseHas('products', ['id' => $product->id, 'disposal_status' => 'disposed']);
    }
}
