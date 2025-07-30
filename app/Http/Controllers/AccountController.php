<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function index(Request $request)
    {
        $member = $request->user();

        $accounts = Account::where('member_id', $member->id)->get();

        return response()->json($accounts);
    }

    public function store(Request $request)
    {
        $member = $request->user();

        $data = $request->validate([
            'name' => 'required|string|max:100',
            'type' => 'required|in:bank,wallet',
            'balance' => 'required|numeric|min:0',
        ]);

        $data['member_id'] = $member->id;

        $account = Account::create($data);

        return response()->json($account, 201);
    }

    public function show(Request $request, $id)
    {
        $member = $request->user();

        $account = Account::where('member_id', $member->id)
            ->where('id', $id)
            ->firstOrFail();

        return response()->json($account);
    }

    public function update(Request $request, $id)
    {
        $member = $request->user();

        $account = Account::where('member_id', $member->id)
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
        $member = $request->user();

        $account = Account::where('member_id', $member->id)
            ->where('id', $id)
            ->firstOrFail();

        $account->delete();

        return response()->json(['message' => 'Deleted']);
    }
}
