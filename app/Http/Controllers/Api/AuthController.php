<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil',
            'token' => $token,
            'data' => $user
        ]);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
            'new_password' => ['required', 'min:8', 'confirmed'],
        ]);

        $user = User::where('id', Auth::user()->id)->first();

        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->with('danger', 'Password lama tidak sesuai');
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return redirect('/settings')->with('success', 'Password diperbarui');
    }

    public function user()
    {
        $user = Auth::user();

        return response()->json([
            'message' => 'Data user',
            'data' => $user
        ]);
    }
}
