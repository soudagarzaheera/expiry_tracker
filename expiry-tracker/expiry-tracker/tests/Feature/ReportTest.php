<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_csv_contains_only_their_products(): void
    {
        $user = User::factory()->create();
        Product::factory()->create(['user_id' => $user->id, 'name' => 'My Visible Product']);
        Product::factory()->create(['name' => 'Private Other Product']);

        $response = $this->actingAs($user)->get(route('reports.csv'));

        $response->assertOk()->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $content = $response->streamedContent();
        $this->assertStringContainsString('My Visible Product', $content);
        $this->assertStringNotContainsString('Private Other Product', $content);
    }

    public function test_admin_csv_contains_products_from_all_users(): void
    {
        $admin = User::factory()->admin()->create();
        Product::factory()->create(['name' => 'System Wide Product']);

        $response = $this->actingAs($admin)->get(route('reports.csv'));
        $this->assertStringContainsString('System Wide Product', $response->streamedContent());
    }
}
