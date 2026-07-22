<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

/**
 * Manajemen akun pengguna — dipakai oleh Developer.
 * Semua endpoint butuh login (auth:sanctum). Pembatasan agar hanya
 * developer yang boleh mengelola dilakukan di setiap method (cek role),
 * karena aplikasi belum memakai middleware role khusus.
 */
class UserController extends Controller
{
    // Peran yang boleh dibuat/dikelola
    private array $roleValid = ['admin', 'sekdes', 'kades', 'warga', 'developer'];

    // Pastikan yang mengakses adalah developer
    private function pastikanDeveloper(Request $request)
    {
        $u = $request->user();
        if (!$u || $u->role !== 'developer') {
            abort(response()->json([
                'status'  => 'error',
                'message' => 'Akses ditolak. Hanya Developer yang boleh mengelola akun.',
            ], 403));
        }
    }

    // Daftar semua pengguna (opsional filter role & pencarian)
    public function index(Request $request)
    {
        $this->pastikanDeveloper($request);

        $q = User::query();
        if ($request->filled('role')) {
            $q->where('role', $request->role);
        }
        if ($request->filled('cari')) {
            $cari = $request->cari;
            $q->where(function ($w) use ($cari) {
                $w->where('nama_lengkap', 'like', "%{$cari}%")
                    ->orWhere('username', 'like', "%{$cari}%");
            });
        }

        $users = $q->orderBy('role')->orderBy('nama_lengkap')->get();

        return response()->json(['status' => 'success', 'data' => $users]);
    }

    // Buat akun baru
    public function store(Request $request)
    {
        $this->pastikanDeveloper($request);

        $data = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'username'     => 'required|string|max:255|unique:users,username',
            'password'     => 'required|string|min:6',
            'role'         => ['required', Rule::in($this->roleValid)],
        ]);

        $user = User::create([
            'nama_lengkap' => $data['nama_lengkap'],
            'username'     => $data['username'],
            'password'     => Hash::make($data['password']),
            'role'         => $data['role'],
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Akun berhasil dibuat.',
            'data'    => $user,
        ], 201);
    }

    // Perbarui data akun (password opsional)
    public function update(Request $request, $id)
    {
        $this->pastikanDeveloper($request);

        $user = User::findOrFail($id);

        $data = $request->validate([
            'nama_lengkap' => 'sometimes|required|string|max:255',
            'username'     => ['sometimes', 'required', 'string', 'max:255', Rule::unique('users', 'username')->ignore($user->id)],
            'password'     => 'nullable|string|min:6',
            'role'         => ['sometimes', 'required', Rule::in($this->roleValid)],
        ]);

        if (!empty($data['nama_lengkap'])) $user->nama_lengkap = $data['nama_lengkap'];
        if (!empty($data['username']))     $user->username = $data['username'];
        if (!empty($data['role']))         $user->role = $data['role'];
        if (!empty($data['password']))     $user->password = Hash::make($data['password']);
        $user->save();

        return response()->json([
            'status'  => 'success',
            'message' => 'Akun berhasil diperbarui.',
            'data'    => $user,
        ]);
    }

    // Hapus akun (tidak boleh menghapus diri sendiri)
    public function destroy(Request $request, $id)
    {
        $this->pastikanDeveloper($request);

        if ((int) $request->user()->id === (int) $id) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Tidak bisa menghapus akun sendiri.',
            ], 422);
        }

        $user = User::findOrFail($id);
        $user->tokens()->delete(); // cabut semua sesi login akun itu
        $user->delete();

        return response()->json(['status' => 'success', 'message' => 'Akun dihapus.']);
    }

    // Reset password cepat
    public function resetPassword(Request $request, $id)
    {
        $this->pastikanDeveloper($request);

        $data = $request->validate([
            'password' => 'required|string|min:6',
        ]);

        $user = User::findOrFail($id);
        $user->password = Hash::make($data['password']);
        $user->save();
        $user->tokens()->delete(); // paksa login ulang dgn password baru

        return response()->json(['status' => 'success', 'message' => 'Password direset.']);
    }
}
