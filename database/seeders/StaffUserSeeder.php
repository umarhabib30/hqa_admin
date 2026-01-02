<?php

// database/seeders/StaffUserSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class StaffUserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Staff',
            'email' => 'staff@hqa.com',
            'password' => Hash::make('password123'),
        ]);
    }
}
