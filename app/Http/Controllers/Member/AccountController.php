<?php

namespace App\Http\Controllers\Member;

use App\Models\Account;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AccountController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $accounts = Account::whereIn('member_id', $user->members->pluck('id'))->get();

        return response()->json($accounts);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'member_id' => 'required|exists:members,id',
            'name' => 'required|string|max:100',
            'type' => 'required|in:bank,wallet',
            'balance' => 'required|numeric|min:0',
        ]);

        // Validasi bahwa member milik user yang sedang login
        if (! $user->members->pluck('id')->contains($data['member_id'])) {
            return response()->json(['message' => 'Unauthorized member.'], 403);
        }

        $account = Account::create($data);

        return response()->json($account, 201);
    }

    public function show(Request $request, $id)
    {
        $user = $request->user();

        $account = Account::where('id', $id)
            ->whereIn('member_id', $user->members->pluck('id'))
            ->firstOrFail();

        return response()->json($account);
    }

    public function update(Request $request, $id)
    {
        $user = $request->user();

        $account = Account::where('id', $id)
            ->whereIn('member_id', $user->members->pluck('id'))
            ->firstOrFail();

        $data = $request->validate([
            'name' => 'sometimes|string|max:100',
            'type' => 'sometimes|in:bank,wallet',
            'balance' => 'sometimes|numeric|min:0',
        ]);

        $account->update($data);

        return response()->json($account);
    }

    public function destroy(Request $request, $id)
    {
        $user = $request->user();

        $account = Account::where('id', $id)
            ->whereIn('member_id', $user->members->pluck('id'))
            ->firstOrFail();

        $account->delete();

        return response()->json(['message' => 'Deleted']);
    }
}
