<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Member;

class MemberAuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'member_id' => 'required|exists:members,id',
        ]);

        $member = Member::findOrFail($request->member_id);

        // Login sukses, generate token Sanctum
        $token = $member->createToken('member-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'member' => $member,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out']);
    }
}
