<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        // 2. Cari User berdasarkan username
        $user = User::where('username', $request->username)->first();

        // 3. Cek user ada DAN password cocok (pakai Hash::check, aman)
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Username atau Password salah!',
            ], 401);
        }

        // 4. Buat token Sanctum untuk sesi login
        $token = $user->createToken('auth_token')->plainTextToken;

        // 5. Berhasil — kirim data user + token
        return response()->json([
            'status'  => 'success',
            'message' => 'Login Berhasil!',
            'user'    => $user,
            'token'   => $token,
        ], 200);
    }

    // Logout: hapus token yang sedang dipakai
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Berhasil logout.',
        ]);
    }

    // Ambil data user yang sedang login (buat verifikasi sesi)
    public function me(Request $request)
    {
        return response()->json([
            'status' => 'success',
            'user'   => $request->user(),
        ]);
    }
}
