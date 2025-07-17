<?php

namespace Database\Seeders;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;
use App\Models\UnitCluster; // Import UnitCluster model

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID'); // Gunakan locale Indonesia untuk nomor telepon

        // Data pengguna spesifik
        $specificUsersData = [
            [
                'name' => 'Pengelola BPU',
                'email' => 'admin@test.com',
                'phone' => $this->generateRandomPhoneNumber($faker),
                'password' => 'password',
                'role' => 'admin',
            ],
            [
                'name' => 'Kepala Rusunawa',
                'email' => 'kepalarusunawa@test.com',
                'phone' => $this->generateRandomPhoneNumber($faker),
                'password' => 'password',
                'role' => 'head_of_rusunawa',
            ],
            [
                'name' => 'Staf 1 Rusunawa',
                'email' => 'staf1rusunawa@test.com',
                'phone' => $this->generateRandomPhoneNumber($faker),
                'password' => 'password',
                'role' => 'staff_of_rusunawa',
            ],
            [
                'name' => 'Staf 2 Rusunawa',
                'email' => 'staf2rusunawa@test.com',
                'phone' => $this->generateRandomPhoneNumber($faker),
                'password' => 'password',
                'role' => 'staff_of_rusunawa',
            ],
            [
                'name' => 'Staf 3 Rusunawa',
                'email' => 'staf3rusunawa@test.com',
                'phone' => $this->generateRandomPhoneNumber($faker),
                'password' => 'password',
                'role' => 'staff_of_rusunawa',
            ],
        ];

        // Proses pengguna spesifik
        foreach ($specificUsersData as $userData) {
            $user = User::where('email', $userData['email'])->first();

            if (!$user) {
                $user = User::create([
                    'name' => $userData['name'],
                    'email' => $userData['email'],
                    'phone' => $userData['phone'],
                    'password' => Hash::make($userData['password']),
                ]);

                $role = Role::where('name', $userData['role'])->first();
                if ($role) {
                    $user->assignRole($role);
                } else {
                    \Illuminate\Support\Facades\Log::warning("Role '{$userData['role']}' tidak ditemukan untuk user '{$userData['email']}'");
                }
            }
        }

        // Pastikan role staff_of_rusunawa ada
        $staffRole = Role::firstOrCreate(['name' => 'staff_of_rusunawa']);

        // Buat 1 user random dengan role staff_of_rusunawa (as per original loop limit)
        for ($i = 0; $i < 1; $i++) {
            $email = $faker->unique()->safeEmail;

            // Cek apakah email sudah ada
            $user = User::where('email', $email)->first();

            if (!$user) {
                $user = User::create([
                    'name' => $faker->name,
                    'email' => $email,
                    'phone' => $this->generateRandomPhoneNumber($faker),
                    'password' => Hash::make('password'),
                ]);

                // Assign role staff_of_rusunawa
                $user->assignRole($staffRole);
            }
        }

        // Assign unit clusters to staff users
        // Ensure UnitClusterSeeder runs before UserSeeder for this to work correctly
        $gedungA = UnitCluster::where('name', 'Gedung A')->first();
        $gedungB = UnitCluster::where('name', 'Gedung B')->first();

        // Assign 'Staf 1 Rusunawa' to Gedung A and Gedung B
        $staf1 = User::where('email', 'staf1rusunawa@test.com')->first();
        if ($staf1 && $gedungA && $gedungB) {
            $staf1->unitClusters()->sync([$gedungA->id, $gedungB->id]);
        }

        // Assign 'Staf 2 Rusunawa' to Gedung A
        $staf2 = User::where('email', 'staf2rusunawa@test.com')->first();
        if ($staf2 && $gedungA) {
            $staf2->unitClusters()->sync([$gedungA->id]);
        }

        // Staf 3 Rusunawa will not be assigned any clusters by default
    }

    /**
     * Generate random phone number in format 081234567890 or +6281234567890
     */
    private function generateRandomPhoneNumber($faker): string
    {
        // Generate 10-digit number starting with 081-089
        $number = '08' . $faker->numberBetween(1, 9) . $faker->numerify('#######');

        // Randomly choose format: 081234567890 or +6281234567890
        return $faker->randomElement([
            $number,
            '+62' . substr($number, 1),
        ]);
    }
}