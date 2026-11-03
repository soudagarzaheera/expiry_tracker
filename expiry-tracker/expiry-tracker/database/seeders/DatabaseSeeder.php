<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::updateOrCreate(['email' => 'admin@expirytracker.test'], [
            'name' => 'System Administrator', 'password' => 'Admin@123', 'role' => 'admin',
            'is_active' => true, 'email_verified_at' => now(),
        ]);
        $user = User::updateOrCreate(['email' => 'user@expirytracker.test'], [
            'name' => 'Demo User', 'password' => 'User@123', 'role' => 'user',
            'is_active' => true, 'email_verified_at' => now(),
        ]);

        $categories = collect([
            'Medicines' => 'Medicines, tablets, syrups and healthcare supplies.',
            'Groceries' => 'Packaged food and household grocery items.',
            'Cosmetics' => 'Beauty, skincare and personal care products.',
            'Dairy Products' => 'Milk, cheese, yoghurt and other dairy items.',
            'Beverages' => 'Juices, soft drinks and bottled beverages.',
            'Other' => 'Products that do not belong to another category.',
        ])->mapWithKeys(fn ($description, $name) => [$name => Category::updateOrCreate(['name' => $name], ['description' => $description, 'is_active' => true])]);

        $samples = [
            ['Vitamin Tablets', 'Medicines', 'MED-1001', 20, 'Boxes', -60, 120, 'Daily multivitamin tablets.'],
            ['Breakfast Cereal', 'Groceries', 'GRO-2044', 14, 'Packets', -30, 18, 'Sample product nearing expiry.'],
            ['Face Moisturiser', 'Cosmetics', 'COS-7310', 8, 'Bottles', -180, 75, 'Dermatologically tested moisturiser.'],
            ['Fresh Yoghurt', 'Dairy Products', 'DAI-3012', 24, 'Cups', -12, -2, 'Expired demo item ready for disposal.'],
            ['Orange Juice', 'Beverages', 'BEV-8821', 12, 'Bottles', -40, 7, 'Keep refrigerated after opening.'],
            ['First Aid Kit', 'Other', 'OTH-1110', 3, 'Kits', -90, -10, 'Expired first aid supplies.'],
        ];

        foreach ($samples as [$name, $category, $batch, $quantity, $unit, $mfgDays, $expiryDays, $description]) {
            Product::updateOrCreate(['batch_number' => $batch], [
                'user_id' => $user->id, 'category_id' => $categories[$category]->id, 'name' => $name,
                'quantity' => $quantity, 'unit' => $unit, 'manufacturing_date' => now()->addDays($mfgDays),
                'expiry_date' => now()->addDays($expiryDays), 'description' => $description,
                'disposal_status' => 'active',
            ]);
        }
    }
}
