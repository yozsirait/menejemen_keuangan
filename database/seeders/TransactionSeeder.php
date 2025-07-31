<?php

namespace Database\Seeders;

use App\Models\Transaction;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        $userId = 3;
        $memberId = 1;
        $accountId = 2;
        $categoryIds = [2, 3, 4];
        $types = ['income', 'expense'];

        foreach (range(1, 20) as $i) {
            $type = Arr::random($types);
            $amount = $type === 'income'
                ? rand(100000, 1000000)
                : rand(50000, 500000);

            Transaction::create([
                'user_id' => $userId,
                'member_id' => $memberId,
                'account_id' => $accountId,
                'category_id' => Arr::random($categoryIds),
                'type' => $type,
                'amount' => $amount,
                'description' => ucfirst($type) . " transaksi ke-$i",
                'date' => Carbon::now()->subDays(rand(0, 30)),
            ]);
        }
    }
}
