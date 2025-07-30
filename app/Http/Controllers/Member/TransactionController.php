<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Account;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $member = $request->user();

        $transactions = Transaction::where('member_id', $member->id)
            ->with(['account'])
            ->latest()
            ->get();

        return response()->json($transactions);
    }

    public function store(Request $request)
    {
        $member = $request->user();

        $validated = $request->validate([
            'account_id' => 'nullable|exists:accounts,id',
            'type' => 'required|in:income,expense',
            'category' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'date' => 'required|date',
        ]);

        if ($validated['account_id'] ?? false) {
            Account::where('id', $validated['account_id'])
                ->where('member_id', $member->id)
                ->firstOrFail();
        }

        $transaction = Transaction::create([
            'user_id' => $member->user_id,
            'member_id' => $member->id,
            'account_id' => $validated['account_id'] ?? null,
            'type' => $validated['type'],
            'category' => $validated['category'],
            'amount' => $validated['amount'],
            'description' => $validated['description'] ?? null,
            'date' => $validated['date'],
        ]);

        return response()->json($transaction, 201);
    }

    public function show(Request $request, $id)
    {
        $member = $request->user();

        $transaction = Transaction::where('id', $id)
            ->where('member_id', $member->id)
            ->firstOrFail();

        return response()->json($transaction);
    }

    public function update(Request $request, $id)
    {
        $member = $request->user();

        $transaction = Transaction::where('id', $id)
            ->where('member_id', $member->id)
            ->firstOrFail();

        $validated = $request->validate([
            'account_id' => 'nullable|exists:accounts,id',
            'type' => 'sometimes|in:income,expense',
            'category' => 'sometimes|string',
            'amount' => 'sometimes|numeric|min:0',
            'description' => 'nullable|string',
            'date' => 'sometimes|date',
        ]);

        if (isset($validated['account_id'])) {
            Account::where('id', $validated['account_id'])
                ->where('member_id', $member->id)
                ->firstOrFail();
        }

        $transaction->update($validated);

        return response()->json($transaction);
    }

    public function destroy(Request $request, $id)
    {
        $member = $request->user();

        $transaction = Transaction::where('id', $id)
            ->where('member_id', $member->id)
            ->firstOrFail();

        $transaction->delete();

        return response()->json(['message' => 'Deleted']);
    }
}
