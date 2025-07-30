<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Member;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $accounts = Account::whereIn('member_id', function ($query) use ($user) {
            $query->select('id')
                ->from('members')
                ->where('user_id', $user->id);
        })->with('member')->get();

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

        // Pastikan member milik user
        $member = Member::where('id', $data['member_id'])
            ->where('user_id', $user->id)
            ->firstOrFail();

        $account = Account::create([
            'member_id' => $member->id,
            'name' => $data['name'],
            'type' => $data['type'],
            'balance' => $data['balance'],
        ]);

        return response()->json($account, 201);
    }

    public function show(Request $request, $id)
    {
        $user = $request->user();

        $account = Account::with('member')
            ->whereHas('member', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->where('id', $id)
            ->firstOrFail();

        return response()->json($account);
    }

    public function update(Request $request, $id)
    {
        $user = $request->user();

        $account = Account::whereHas('member', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->where('id', $id)
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

        $account = Account::whereHas('member', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->where('id', $id)
            ->firstOrFail();

        $account->delete();

        return response()->json(['message' => 'Deleted']);
    }
}
