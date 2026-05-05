<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ExtraProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'name' => 'Power Bank',
                'price' => 2500,
                'cost_price' => 1500,
            ],
            [
                'name' => 'SIM Ejector Pin',
                'price' => 50,
                'cost_price' => 10,
            ],
            [
                'name' => 'Keyboard',
                'price' => 1200,
                'cost_price' => 800,
            ],
            [
                'name' => 'Mouse (Bluetooth)',
                'price' => 800,
                'cost_price' => 500,
            ],
            [
                'name' => 'Mouse (Cabled)',
                'price' => 400,
                'cost_price' => 250,
            ],
            [
                'name' => 'Bluetooth Ear Pieces',
                'price' => 1500,
                'cost_price' => 900,
            ],
            [
                'name' => 'HDMI Cable',
                'price' => 600,
                'cost_price' => 300,
            ],
            [
                'name' => 'Smart Watch',
                'price' => 3500,
                'cost_price' => 2000,
            ],
            [
                'name' => 'WiFi Router',
                'price' => 4500,
                'cost_price' => 3000,
            ],
            [
                'name' => 'Remote',
                'price' => 500,
                'cost_price' => 250,
            ],
        ];

        foreach ($products as $index => $productData) {
            Product::create([
                'category_id' => 1,
                'name' => $productData['name'],
                'sku' => 'EXT-' . strtoupper(substr(md5(uniqid()), 0, 6)),
                'price' => $productData['price'],
                'cost_price' => $productData['cost_price'],
                'stock_quantity' => 50,
                'reorder_level' => 10,
                'is_active' => true,
                'warranty_months' => 6,
                'serial_tracking' => false,
            ]);
        }
    }
}
