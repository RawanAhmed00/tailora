<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name'=>'Rawan Ahmed',
            'email'=>'ahmdrwan577@gmail.com',
            'password'=>Hash::make('Rawan2005'),
            'role'=>'user',
            'age'=>'20',
            'dist_country'=>'Egypt',
            'gender'=>'Female',
            'phone_num'=>'0110000000',
            'email_verified_at'=>now(),
        ]);
        User::create([
            'name'=>'user',
            'email'=>'user@gmail.com',
            'password'=>Hash::make('pass123'),
            'role'=>'user',
            'age'=>'20',
            'dist_country'=>'Egypt',
            'gender'=>'Male',
            'phone_num'=>'0110010000',
            'email_verified_at'=>now(),
        ]);

    }
}