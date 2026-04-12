<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (Product::count() > 0) {
            return;
        }

        Product::insert([
            [
                'name' => 'iPhone 15 Pro',
                'category' => 'Téléphones',
                'quantity' => 5,
                'purchase_price' => 8500,
                'transport_fees' => 150,
                'other_fees' => 0,
                'selling_price' => 9800,
                'notes' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Télé Samsung 55"',
                'category' => 'TV',
                'quantity' => 2,
                'purchase_price' => 3200,
                'transport_fees' => 200,
                'other_fees' => 0,
                'selling_price' => 4100,
                'notes' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'AirPods Pro 2',
                'category' => 'Audio',
                'quantity' => 0,
                'purchase_price' => 950,
                'transport_fees' => 80,
                'other_fees' => 0,
                'selling_price' => 1250,
                'notes' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Laptop HP 15',
                'category' => 'Informatique',
                'quantity' => 3,
                'purchase_price' => 4800,
                'transport_fees' => 120,
                'other_fees' => 0,
                'selling_price' => 6200,
                'notes' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
