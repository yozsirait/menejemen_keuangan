<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Member;
use App\Models\Category;
use App\Models\Budget;
use App\Models\Transaction;
use Illuminate\Support\Carbon;

class DummyFinanceSeeder extends Seeder
{
    public function run(): void
    {
        $userId = 3;
        $memberId = 1;

        // 1. Member (pastikan ini id=1 atau disesuaikan)
        Member::updateOrCreate(
            ['id' => $memberId],
            ['user_id' => $userId, 'name' => 'Yosua', 'role' => 'suami']
        );

        // 2. Categories
        $categories = [
            ['name' => 'Gaji', 'type' => 'income'],
            ['name' => 'Transportasi', 'type' => 'expense'],
            ['name' => 'Makanan', 'type' => 'expense'],
            ['name' => 'Lain-lain', 'type' => 'expense'],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(
                ['user_id' => $userId, 'name' => $cat['name']],
                ['type' => $cat['type']]
            );
        }

        // Ambil ulang kategori yang sudah dibuat
        $categoryTransport = Category::where('user_id', $userId)->where('name', 'Transportasi')->first();
        $categoryMakan = Category::where('user_id', $userId)->where('name', 'Makanan')->first();

        // 3. Budgets (hanya untuk tipe expense)
        $month = Carbon::now()->format('Y-m');

        Budget::updateOrCreate(
            ['user_id' => $userId, 'category_id' => $categoryTransport->id, 'month' => $month],
            ['amount' => 500000]
        );

        Budget::updateOrCreate(
            ['user_id' => $userId, 'category_id' => $categoryMakan->id, 'month' => $month],
            ['amount' => 1000000]
        );

        // 4. Transactions
        $now = Carbon::now();

        $transactions = [
            [
                'type' => 'income',
                'category' => 'Gaji',
                'amount' => 10000000,
                'description' => 'Gaji bulan ini',
                'date' => $now->copy()->subDays(5),
            ],
            [
                'type' => 'expense',
                'category' => 'Transportasi',
                'amount' => 20000,
                'description' => 'Naik ojek',
                'date' => $now->copy()->subDays(4),
            ],
            [
                'type' => 'expense',
                'category' => 'Makanan',
                'amount' => 75000,
                'description' => 'Makan siang',
                'date' => $now->copy()->subDays(2),
            ],
        ];

        foreach ($transactions as $tx) {
            Transaction::create([
                'user_id' => $userId,
                'member_id' => $memberId,
                'type' => $tx['type'],
                'category' => $tx['category'],
                'amount' => $tx['amount'],
                'description' => $tx['description'],
                'date' => $tx['date'],
            ]);
        }
    }
}
