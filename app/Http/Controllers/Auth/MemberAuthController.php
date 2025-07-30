<?php

// app/Http/Controllers/Auth/MemberAuthController.php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Member;
use Illuminate\Support\Facades\Hash;

class MemberAuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $member = Member::where('email', $request->email)->first();

        if (!$member || !Hash::check($request->password, $member->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $member->createToken('member-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'member' => $member,
        ]);
    }
}
