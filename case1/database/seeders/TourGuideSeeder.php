<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TourGuideSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name'=>'guide',
            'email'=>'guide1@example.com',
            'password'=>Hash::make('guide1@example.com'),
            'role'=>'t_guide',
            'age'=>'40',
            'dist_country'=>'Egypt',
            'gender'=>'male',
            'phone_num'=>'0110000447',
            'email_verified_at'=>now(),
        ]);
    }
}
