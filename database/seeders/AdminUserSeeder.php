<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@hqa.com',
                'password' => 'superadmin123',
                'role' => 'super_admin',
            ],
            [
                'name' => 'Waqas tahir',
                'email' => 'waqastahir909090@gmail.com',
                'password' => 'waqas@1122',
                'role' => 'admin',
            ],
            [
                'name' => 'Admin',
                'email' => 'michael.johnson202521@gmail.com',
                'password' => 'waqas123',
                'role' => 'super_admin',
            ],
            [
                'name' => 'Iqrash',
                'email' => 'iqrashahmad218@gmail.com',
                'password' => 'iqrash123',
                'role' => 'admin',
            ]
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['email' => $user['email']],
                [
                    'name' => $user['name'],
                    'password' => Hash::make($user['password']),
                    'role' => $user['role'] ?? 'admin',
                ]
            );
        }
    }
}
