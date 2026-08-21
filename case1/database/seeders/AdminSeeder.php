<?php

namespace Database\Seeders;


use App\Models\User;
use Illuminate\Database\Seeder;


use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{

    public function run(): void
    {
        User::create([
            'name' => 'Ammar Khaled',
            'email' => 'Ammarkhaled2005@gmail.com',
            'password' => Hash::make('Ammar3051'),
            'role' => 'admin',
            'age' => '30',
            'dist_country' => 'Egypt',
            'gender' => 'Male',
            'phone_num' => '01000000001',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Farah Yasser',
            'email' => 'farahyasser04@gmail.com',
            'password' => Hash::make('farah123'),
            'role' => 'admin',
            'age' => '32',
            'dist_country' => 'Egypt',
            'gender' => 'Female',
            'phone_num' => '01000000002',
            'email_verified_at' => now(),
        ]);
    }
}

   