<?php
// app/Http/Controllers/TransactionController.php
namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $transactions = Transaction::where('user_id', $user->user_id ?? $user->id)->get();
        return response()->json($transactions);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $validated = $request->validate([
            'member_id' => 'required|exists:members,id',
            'type' => 'required|in:income,expense',
            'category' => 'required|string',
            'amount' => 'required|numeric',
            'date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        $transaction = Transaction::create([
            ...$validated,
            'user_id' => $user->user_id ?? $user->id,
        ]);

        return response()->json($transaction, 201);
    }

    public function show($id)
    {
        $user = Auth::user();
        $transaction = Transaction::where('id', $id)
            ->where('user_id', $user->user_id ?? $user->id)
            ->firstOrFail();

        return response()->json($transaction);
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $transaction = Transaction::where('id', $id)
            ->where('user_id', $user->user_id ?? $user->id)
            ->firstOrFail();

        $transaction->update($request->only('type', 'category', 'amount', 'date', 'description'));

        return response()->json($transaction);
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $transaction = Transaction::where('id', $id)
            ->where('user_id', $user->user_id ?? $user->id)
            ->firstOrFail();

        $transaction->delete();

        return response()->json(['message' => 'Deleted']);
    }
}
