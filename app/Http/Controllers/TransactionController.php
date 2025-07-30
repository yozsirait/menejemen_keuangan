<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Member;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $transactions = Transaction::where('user_id', $user->id)
            ->with('member')
            ->latest()
            ->get();

        return response()->json($transactions);
    }

    public function store(Request $request)
    {
        $request->validate([
            'member_id'   => 'required|exists:members,id',
            'type'        => 'required|in:income,expense',
            'category'    => 'required|string',
            'amount'      => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'date'        => 'required|date',
        ]);

        $user = $request->user();
        $member = Member::findOrFail($request->member_id);

        if ($member->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $transaction = Transaction::create([
            'user_id'     => $user->id,
            'member_id'   => $member->id,
            'type'        => $request->type,
            'category'    => $request->category,
            'amount'      => $request->amount,
            'description' => $request->description,
            'date'        => $request->date,
        ]);

        return response()->json($transaction, 201);
    }

    public function show(Request $request, $id)
    {
        $user = $request->user();

        $transaction = Transaction::where('id', $id)
            ->where('user_id', $user->id)
            ->with('member')
            ->firstOrFail();

        return response()->json($transaction);
    }

    public function update(Request $request, $id)
    {
        $user = $request->user();

        $transaction = Transaction::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $validated = $request->validate([
            'type'        => 'sometimes|in:income,expense',
            'category'    => 'sometimes|string',
            'amount'      => 'sometimes|numeric|min:0',
            'description' => 'nullable|string',
            'date'        => 'sometimes|date',
        ]);

        $transaction->update($validated);

        return response()->json($transaction);
    }

    public function destroy(Request $request, $id)
    {
        $user = $request->user();

        $transaction = Transaction::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $transaction->delete();

        return response()->json(['message' => 'Deleted']);
    }
}
