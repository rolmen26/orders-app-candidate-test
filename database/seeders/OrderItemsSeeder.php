<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OrderItemsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $orderItems = [
            // Order 1
            [
                'order_id' => 1,
                'product_id' => 1,
                'quantity' => 1,
                'unit_price' => 899.99,
                'subtotal' => 899.99,
                'created_at' => Carbon::parse('2026-02-15 10:30:00'),
                'updated_at' => Carbon::parse('2026-02-15 10:30:00'),
            ],
            // Order 2
            [
                'order_id' => 2,
                'product_id' => 3,
                'quantity' => 1,
                'unit_price' => 99.99,
                'subtotal' => 99.99,
                'created_at' => Carbon::parse('2026-02-20 14:45:00'),
                'updated_at' => Carbon::parse('2026-02-20 14:45:00'),
            ],
            [
                'order_id' => 2,
                'product_id' => 4,
                'quantity' => 1,
                'unit_price' => 149.99,
                'subtotal' => 149.99,
                'created_at' => Carbon::parse('2026-02-20 14:45:00'),
                'updated_at' => Carbon::parse('2026-02-20 14:45:00'),
            ],
            // Order 3
            [
                'order_id' => 3,
                'product_id' => 2,
                'quantity' => 1,
                'unit_price' => 1299.99,
                'subtotal' => 1299.99,
                'created_at' => Carbon::parse('2026-03-01 09:15:00'),
                'updated_at' => Carbon::parse('2026-03-01 09:15:00'),
            ],
            // Order 4
            [
                'order_id' => 4,
                'product_id' => 14,
                'quantity' => 2,
                'unit_price' => 29.99,
                'subtotal' => 59.98,
                'created_at' => Carbon::parse('2026-02-28 16:20:00'),
                'updated_at' => Carbon::parse('2026-02-28 16:20:00'),
            ],
            [
                'order_id' => 4,
                'product_id' => 15,
                'quantity' => 1,
                'unit_price' => 59.99,
                'subtotal' => 59.99,
                'created_at' => Carbon::parse('2026-02-28 16:20:00'),
                'updated_at' => Carbon::parse('2026-02-28 16:20:00'),
            ],
            [
                'order_id' => 4,
                'product_id' => 11,
                'quantity' => 1,
                'unit_price' => 19.99,
                'subtotal' => 19.99,
                'created_at' => Carbon::parse('2026-02-28 16:20:00'),
                'updated_at' => Carbon::parse('2026-02-28 16:20:00'),
            ],
        ];
        DB::table('order_items')->insert($orderItems);
        $this->command->info('✅ Order items seeded successfully!');
    }
}
