<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Member;
use App\Models\Account;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        return Transaction::with(['member', 'account'])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'member_id' => 'required|exists:members,id',
            'account_id' => 'nullable|exists:accounts,id',
            'type' => 'required|in:income,expense',
            'category' => 'required|string',
            'amount' => 'required|numeric',
            'description' => 'nullable|string',
            'date' => 'required|date',
        ]);

        $member = Member::where('id', $data['member_id'])
            ->where('user_id', $user->id)
            ->firstOrFail();

        if ($data['account_id']) {
            $account = Account::where('id', $data['account_id'])
                ->where('member_id', $member->id)
                ->firstOrFail();
        }

        $transaction = Transaction::create([
            'user_id' => $user->id,
            'member_id' => $member->id,
            'account_id' => $data['account_id'] ?? null,
            'type' => $data['type'],
            'category' => $data['category'],
            'amount' => $data['amount'],
            'description' => $data['description'] ?? null,
            'date' => $data['date'],
        ]);

        return response()->json($transaction, 201);
    }

    public function show(Request $request, $id)
    {
        return Transaction::with(['member', 'account'])
            ->where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();
    }

    public function update(Request $request, $id)
    {
        $transaction = Transaction::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $data = $request->validate([
            'account_id' => 'nullable|exists:accounts,id',
            'type' => 'sometimes|in:income,expense',
            'category' => 'sometimes|string',
            'amount' => 'sometimes|numeric|min:0',
            'description' => 'nullable|string',
            'date' => 'sometimes|date',
        ]);

        if (isset($data['account_id'])) {
            $account = Account::where('id', $data['account_id'])
                ->where('member_id', $transaction->member_id)
                ->firstOrFail();
        }

        $transaction->update($data);

        return response()->json($transaction);
    }

    public function destroy(Request $request, $id)
    {
        $transaction = Transaction::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $transaction->delete();

        return response()->json(['message' => 'Deleted']);
    }
}
