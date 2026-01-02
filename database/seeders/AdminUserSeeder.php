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
                'name' => 'Waqas tahir',
                'email' => 'waqastahir909090@gmail.com',
                'password' => 'waqas@1122',
            ],
            
            [
                'name' => 'Admin',
                'email' => 'michael.johnson202521@gmail.com',
                'password' => 'waqas123',
            ],
            [
                'name' => 'Iqrash',
                'email' => 'iqrashahmad218@gmail.com',
                'password' => 'iqrash123',
            ]
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['email' => $user['email']],
                [
                    'name' => $user['name'],
                    'password' => Hash::make($user['password']),
                ]
            );
        }
    }
}
