<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => ['required', 'email'],
                'password' => ['required'],
            ]);

            if (!Auth::attempt($request->only('email', 'password'))) {
                return ResponseFormatter::error(null, 'Email atau Kata Sandi anda salah!', 401);
            }

            $user = Auth::user();

            return ResponseFormatter::success([
                'token' => $user->createToken("API TOKEN")->plainTextToken,
                'user' => $user,
            ]);
        } catch (\Throwable $th) {
            return ResponseFormatter::error(null, $th->getMessage);
        }
    }

    public function updatePassword(Request $request)
    {
        try {
            $request->validate([
                'current_password' => ['required'],
                'new_password' => ['required', 'min:8', 'confirmed'],
            ]);

            $user = User::where('id', Auth::user()->id)->first();

            if (!Hash::check($request->current_password, $user->password)) {
                return ResponseFormatter::error(null, 'Password lama tidak sesuai');
            }

            $user->update([
                'password' => Hash::make($request->new_password),
            ]);

            return ResponseFormatter::success($user);
        } catch (\Throwable $th) {
            return ResponseFormatter::error(null, $th->getMessage);
        }
    }

    public function user()
    {
        try {
            $user = Auth::user();

            return ResponseFormatter::success($user);
        } catch (\Throwable $th) {
            return ResponseFormatter::error(null, $th->getMessage);
        }
    }
}
