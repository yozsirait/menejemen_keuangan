<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Member;
use App\Models\Account;
use App\Services\AccountBalanceService;

class TransactionController extends Controller
{
    protected $balanceService;

    public function __construct(AccountBalanceService $balanceService)
    {
        $this->balanceService = $balanceService;
    }

    public function index(Request $request)
    {
        $user = $request->user();

        $transactions = Transaction::where('user_id', $user->id)
            ->with(['member', 'account', 'category'])
            ->latest()
            ->get();

        return response()->json($transactions);
    }

    public function store(Request $request)
    {
        $request->validate([
            'member_id' => 'required|exists:members,id',
            'account_id' => 'nullable|exists:accounts,id',
            'type' => 'required|in:income,expense',
            'category_id' => 'required|exists:categories,id',
            'amount' => 'required|numeric',
            'description' => 'nullable|string',
            'date' => 'required|date',
        ]);

        $member = Member::findOrFail($request->member_id);

        if ($member->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized member.'], 403);
        }

        $account = null;
        if ($request->account_id) {
            $account = Account::where('id', $request->account_id)
                ->where('member_id', $member->id)
                ->firstOrFail();
        }

        $transaction = Transaction::create([
            'user_id' => $member->user_id,
            'member_id' => $member->id,
            'account_id' => $request->account_id,
            'type' => $request->type,
            'category_id' => $request->category_id,
            'amount' => $request->amount,
            'description' => $request->description,
            'date' => $request->date,
        ]);

        if ($account) {
            if ($request->type === 'expense' && $request->amount > $account->balance) {
                return response()->json([
                    'message' => 'Insufficient account balance for this expense.'
                ], 422);
            }
        }

        // Apply balance change
        if ($account) {
            $this->balanceService->applyTransaction($account, $transaction->type, $transaction->amount);
        }

        return response()->json($transaction, 201);
    }

    public function update(Request $request, $id)
    {
        $user = $request->user();

        $transaction = Transaction::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $oldAmount = $transaction->amount;
        $oldType = $transaction->type;
        $oldAccountId = $transaction->account_id;

        $validated = $request->validate([
            'account_id' => 'nullable|exists:accounts,id',
            'type' => 'sometimes|in:income,expense',
            'category_id' => 'sometimes|exists:categories,id',
            'amount' => 'sometimes|numeric|min:0',
            'description' => 'nullable|string',
            'date' => 'sometimes|date',
        ]);

        // Revert old balance
        if ($oldAccountId) {
            $oldAccount = Account::find($oldAccountId);
            if ($oldAccount) {
                $this->balanceService->revertTransaction($oldAccount, $oldType, $oldAmount);
            }
        }

        // Cek apakah account_id/type/amount diubah
        $newAccountId = $validated['account_id'] ?? $transaction->account_id;
        $newType = $validated['type'] ?? $transaction->type;
        $newAmount = $validated['amount'] ?? $transaction->amount;

        if ($newAccountId && $newType === 'expense') {
            $account = Account::find($newAccountId);
            if ($account && $newAmount > $account->balance) {
                // Rollback balance revert (biar nggak ngaco saldo sebelumnya)
                if ($oldAccountId && $oldAccount) {
                    $this->balanceService->applyTransaction($oldAccount, $oldType, $oldAmount);
                }

                return response()->json([
                    'message' => 'Insufficient account balance for this updated expense.'
                ], 422);
            }
        }

        // Update data
        $transaction->update($validated);

        // Apply new balance
        if ($transaction->account_id) {
            $newAccount = Account::find($transaction->account_id);
            if ($newAccount) {
                $this->balanceService->applyTransaction($newAccount, $transaction->type, $transaction->amount);
            }
        }

        return response()->json($transaction);
    }


    public function destroy(Request $request, $id)
    {
        $user = $request->user();

        $transaction = Transaction::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        if ($transaction->account_id) {
            $account = Account::find($transaction->account_id);
            if ($account) {
                $this->balanceService->revertTransaction($account, $transaction->type, $transaction->amount);
            }
        }

        $transaction->delete();

        return response()->json(['message' => 'Deleted']);
    }
}
