<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Budget;
use App\Models\Account;
use Carbon\Carbon;
use App\Http\Controllers\Controller;

class DashboardSummaryController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $startOfMonth = Carbon::now()->startOfMonth()->toDateString();
        $endOfMonth = Carbon::now()->endOfMonth()->toDateString();

        // Total income & expense bulan ini
        $totals = Transaction::where('user_id', $user->id)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->selectRaw("type, SUM(amount) as total")
            ->groupBy('type')
            ->pluck('total', 'type');

        $totalIncome = $totals['income'] ?? 0;
        $totalExpense = $totals['expense'] ?? 0;

        // Total saldo dari semua akun user
        $totalAccountBalance = Account::whereHas('member', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })->sum('balance');

        // Budget per kategori bulan ini
        $budgets = Budget::with('category')
            ->where('user_id', $user->id)
            ->where('month', Carbon::now()->format('Y-m'))
            ->get()
            ->map(function ($budget) use ($user, $startOfMonth, $endOfMonth) {
                $spent = Transaction::where('user_id', $user->id)
                    ->where('category_id', $budget->category_id)
                    ->where('type', 'expense')
                    ->whereBetween('date', [$startOfMonth, $endOfMonth])
                    ->sum('amount');

                return [
                    'category_id' => $budget->category_id,
                    'category_name' => $budget->category->name,
                    'budgeted' => $budget->amount,
                    'spent' => $spent,
                    'remaining' => $budget->amount - $spent,
                    'percent_used' => $budget->amount > 0 ? round($spent / $budget->amount * 100, 2) : 0
                ];
            });

        return response()->json([
            'total_income' => $totalIncome,
            'total_expense' => $totalExpense,
            'total_account_balance' => $totalAccountBalance,
            'balance' => $totalAccountBalance - $totalExpense,
            'budget_summary' => $budgets,
        ]);
    }
}
