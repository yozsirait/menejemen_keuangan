<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Models\Member;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Ambil semua member milik user yang sedang login
        $members = Member::where('user_id', $user->id)->get();

        Log::info('User ID:', ['id' => $user->id]);
        Log::info('Members:', Member::where('user_id', $user->id)->pluck('name')->toArray());

        return response()->json($members);
    }

    public function show($id, Request $request)
    {
        $user = $request->user();
        $member = Member::where('user_id', $user->id)->findOrFail($id);
        return response()->json($member);
    }

    public function update($id, Request $request)
    {
        $user = $request->user();
        $member = Member::where('user_id', $user->id)->findOrFail($id);

        $request->validate([
            'name' => 'sometimes|required|string',
            'role' => 'nullable|string',
        ]);

        $member->update($request->only('name', 'role'));

        return response()->json($member);
    }

    public function destroy($id, Request $request)
    {
        $user = $request->user();
        $member = Member::where('user_id', $user->id)->findOrFail($id);
        $member->delete();

        return response()->json(['message' => 'Member deleted successfully.']);
    }

}
