<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductsCatalogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'sku' => 'LAPTOP-001',
                'name' => 'Laptop HP Pavilion 15',
                'price' => 899.99,
                'stock' => 50,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sku' => 'LAPTOP-002',
                'name' => 'MacBook Air M2',
                'price' => 1299.99,
                'stock' => 30,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sku' => 'MOUSE-001',
                'name' => 'Logitech MX Master 3',
                'price' => 99.99,
                'stock' => 100,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sku' => 'KEYBOARD-001',
                'name' => 'Mechanical Keyboard RGB',
                'price' => 149.99,
                'stock' => 75,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sku' => 'MONITOR-001',
                'name' => 'Dell UltraSharp 27"',
                'price' => 449.99,
                'stock' => 40,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sku' => 'HEADSET-001',
                'name' => 'Sony WH-1000XM5',
                'price' => 349.99,
                'stock' => 60,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sku' => 'WEBCAM-001',
                'name' => 'Logitech C920',
                'price' => 79.99,
                'stock' => 120,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sku' => 'TABLET-001',
                'name' => 'iPad Air 5th Gen',
                'price' => 599.99,
                'stock' => 25,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sku' => 'PHONE-001',
                'name' => 'iPhone 14 Pro',
                'price' => 1099.99,
                'stock' => 20,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sku' => 'DOCK-001',
                'name' => 'USB-C Docking Station',
                'price' => 199.99,
                'stock' => 45,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sku' => 'CABLE-001',
                'name' => 'USB-C Cable 2m',
                'price' => 19.99,
                'stock' => 200,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sku' => 'CHARGER-001',
                'name' => '65W USB-C Charger',
                'price' => 49.99,
                'stock' => 150,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sku' => 'BAG-001',
                'name' => 'Laptop Backpack',
                'price' => 79.99,
                'stock' => 80,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sku' => 'MOUSE-002',
                'name' => 'Wireless Mouse Basic',
                'price' => 29.99,
                'stock' => 180,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sku' => 'KEYBOARD-002',
                'name' => 'Wireless Keyboard Slim',
                'price' => 59.99,
                'stock' => 140,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('products_catalog')->insert($products);

        $this->command->info('✅ Products catalog seeded successfully!');
    }
}

