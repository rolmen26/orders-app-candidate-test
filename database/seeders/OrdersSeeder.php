<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OrdersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $orders = [
            [
                'user_id' => 2,
                'subtotal' => 899.99,
                'discount' => 0.00,
                'tax' => 107.99,
                'total' => 1007.98,
                'status' => 'completed',
                'created_at' => Carbon::parse('2026-02-15 10:30:00'),
                'updated_at' => Carbon::parse('2026-02-15 10:30:00'),
            ],
            [
                'user_id' => 2,
                'subtotal' => 249.98,
                'discount' => 24.99,
                'tax' => 27.00,
                'total' => 251.99,
                'status' => 'completed',
                'created_at' => Carbon::parse('2026-02-20 14:45:00'),
                'updated_at' => Carbon::parse('2026-02-20 14:45:00'),
            ],
            [
                'user_id' => 3,
                'subtotal' => 1299.99,
                'discount' => 0.00,
                'tax' => 155.99,
                'total' => 1455.98,
                'status' => 'pending',
                'created_at' => Carbon::parse('2026-03-01 09:15:00'),
                'updated_at' => Carbon::parse('2026-03-01 09:15:00'),
            ],
            [
                'user_id' => 3,
                'subtotal' => 129.98,
                'discount' => 12.99,
                'tax' => 14.04,
                'total' => 131.03,
                'status' => 'completed',
                'created_at' => Carbon::parse('2026-02-28 16:20:00'),
                'updated_at' => Carbon::parse('2026-02-28 16:20:00'),
            ],
        ];
        DB::table('orders')->insert($orders);
        $this->command->info('✅ Orders seeded successfully!');
    }
}
