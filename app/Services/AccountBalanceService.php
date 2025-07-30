<?php

namespace App\Services;

use App\Models\Account;

class AccountBalanceService
{
    public function applyTransaction(Account $account, string $type, float $amount): void
    {
        $delta = $type === 'income' ? $amount : -$amount;
        $account->balance += $delta;
        $account->save();
    }

    public function revertTransaction(Account $account, string $type, float $amount): void
    {
        $delta = $type === 'income' ? -$amount : $amount;
        $account->balance += $delta;
        $account->save();
    }
}
