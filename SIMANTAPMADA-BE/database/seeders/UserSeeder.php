<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Buat akun default untuk tiap role.
     * Password semua: "password123" (sudah di-hash otomatis).
     * Ganti sesuai kebutuhan setelah login berhasil.
     */
    public function run(): void
    {
        $users = [
            [
                'nama_lengkap' => 'Admin Budi',
                'username'     => 'admin',
                'password'     => Hash::make('password123'),
                'role'         => 'admin',
            ],
            [
                'nama_lengkap' => 'Sekretaris Desa',
                'username'     => 'sekdes',
                'password'     => Hash::make('password123'),
                'role'         => 'sekdes',
            ],
            [
                'nama_lengkap' => 'Kepala Desa',
                'username'     => 'kades',
                'password'     => Hash::make('password123'),
                'role'         => 'kades',
            ],
            [
                'nama_lengkap' => 'Warga Macanan',
                'username'     => 'warga',
                'password'     => Hash::make('password123'),
                'role'         => 'warga',
            ],
            [
                'nama_lengkap' => 'Developer',
                'username'     => 'developer',
                'password'     => Hash::make('password123'),
                'role'         => 'developer',
            ],
        ];

        foreach ($users as $u) {
            // updateOrCreate: kalau username sudah ada, di-update; kalau belum, dibuat.
            // Jadi aman dijalankan berkali-kali tanpa bikin duplikat.
            User::updateOrCreate(
                ['username' => $u['username']],
                $u,
            );
        }
    }
}
