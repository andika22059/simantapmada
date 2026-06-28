<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'nama_lengkap' => 'Admin SiMANTAP',
            'username' => 'admin',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);
    }
}
