<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Update data diri: nama, username, dan foto profil.
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'username'     => [
                'required',
                'string',
                'max:255',
                Rule::unique('users', 'username')->ignore($user->id),
            ],
            'foto'         => 'nullable|file|mimes:jpg,jpeg,png|max:2048', // max 2MB
        ]);

        $user->nama_lengkap = $validated['nama_lengkap'];
        $user->username     = $validated['username'];

        // Kalau ada foto baru: hapus foto lama, simpan yang baru
        if ($request->hasFile('foto')) {
            $oldPath = $user->getRawOriginal('foto');
            if ($oldPath) {
                Storage::disk('public')->delete($oldPath);
            }
            $user->foto = $request->file('foto')->store('profil', 'public');
        }

        $user->save();

        return response()->json([
            'status'  => 'success',
            'message' => 'Profil berhasil diperbarui.',
            'user'    => $user,
        ]);
    }

    /**
     * Ganti password: WAJIB verifikasi password lama dulu.
     */
    public function updatePassword(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'password_lama'  => 'required|string',
            'password_baru'  => 'required|string|min:6|confirmed', // butuh field password_baru_confirmation
        ]);

        // Verifikasi password lama
        if (!Hash::check($validated['password_lama'], $user->password)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Password lama salah!',
            ], 422);
        }

        // Simpan password baru (di-hash)
        $user->password = Hash::make($validated['password_baru']);
        $user->save();

        return response()->json([
            'status'  => 'success',
            'message' => 'Password berhasil diubah.',
        ]);
    }
}
