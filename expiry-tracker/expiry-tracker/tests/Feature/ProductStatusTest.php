<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ProductStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_expiry_status_boundaries_are_calculated_dynamically(): void
    {
        Carbon::setTestNow('2026-09-22 10:00:00');

        $safe = Product::factory()->create(['expiry_date' => '2026-10-23']);
        $thirtyDays = Product::factory()->create(['expiry_date' => '2026-10-22']);
        $today = Product::factory()->create(['expiry_date' => '2026-09-22']);
        $expired = Product::factory()->create(['expiry_date' => '2026-09-21']);

        $this->assertSame('safe', $safe->expiry_status);
        $this->assertSame('expiring_soon', $thirtyDays->expiry_status);
        $this->assertSame('expiring_soon', $today->expiry_status);
        $this->assertSame('expired', $expired->expiry_status);
    }
}
