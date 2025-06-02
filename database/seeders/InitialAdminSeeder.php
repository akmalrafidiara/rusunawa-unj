<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class InitialAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cek apakah user sudah ada
        $email = 'admin@test.com';
        $user = User::where('email', $email)->first();

        if (!$user) {
            $user = User::create([
                'name' => 'Pengelola BPU',
                'email' => $email,
                'password' => Hash::make('password'), // password: password
            ]);

            // Assign role 'admin'
            $role = Role::where('name', 'admin')->first();
            if ($role) {
                $user->assignRole($role);
            }
        }
    }
}
