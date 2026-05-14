<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RoleAndAdminSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Role Spatie
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'penyewa']);

        // 2. Buat Akun Admin Pertama
        $admin = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password123'), // Password untuk login
            'telepon' => '081234567890',
            'alamat' => 'Kantor Pusat',
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // 3. Assign (Pasangkan) Role Admin Spatie ke User tersebut
        $admin->assignRole('admin');
    }
}