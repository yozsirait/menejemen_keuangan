<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Account;
use App\Models\Member;
use App\Http\Controllers\Controller;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $member = auth('sanctum')->user();
        $user = $request->user();

        if (!$member instanceof Member) {
            return response()->json(['message' => 'Unauthorized.'], 401);
        }

        $transactions = Transaction::where('user_id', $user->id)
            ->when($request->member_id, fn($q) =>
                $q->where('member_id', $request->member_id))
            ->when($request->category, fn($q) =>
                $q->where('category', $request->category))
            ->when($request->account_id, fn($q) =>
                $q->where('account_id', $request->account_id))
            ->when($request->type, fn($q) =>
                $q->where('type', $request->type))
            ->when($request->date_from, fn($q) =>
                $q->whereDate('date', '>=', $request->date_from))
            ->when($request->date_to, fn($q) =>
                $q->whereDate('date', '<=', $request->date_to))
            ->with(['member', 'account']) // pastikan relasi account ada
            ->latest()
            ->get();

        return response()->json($transactions);
    }

    public function store(Request $request)
    {
        $member = auth('sanctum')->user();

        if (!$member instanceof Member) {
            return response()->json(['message' => 'Unauthorized.'], 401);
        }

        $validated = $request->validate([
            'account_id' => 'nullable|exists:accounts,id',
            'type' => 'required|in:income,expense',
            'category' => 'required|string',
            'amount' => 'required|numeric',
            'description' => 'nullable|string',
            'date' => 'required|date',
        ]);

        if ($validated['account_id'] ?? false) {
            $account = Account::where('id', $validated['account_id'])
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

    public function update(Request $request, $id)
    {
        $member = auth('sanctum')->user();

        if (!$member instanceof Member) {
            return response()->json(['message' => 'Unauthorized.'], 401);
        }

        $transaction = Transaction::where('id', $id)
            ->where('user_id', $member->user_id)
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
            $account = Account::where('id', $validated['account_id'])
                ->where('member_id', $member->id)
                ->firstOrFail();
        }

        $transaction->update($validated);

        return response()->json($transaction);
    }

    public function destroy(Request $request, $id)
    {
        $member = auth('sanctum')->user();

        if (!$member instanceof Member) {
            return response()->json(['message' => 'Unauthorized.'], 401);
        }

        $transaction = Transaction::where('id', $id)
            ->where('user_id', $member->user_id)
            ->firstOrFail();

        $transaction->delete();

        return response()->json(['message' => 'Deleted']);
    }
}
