<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            [
                'firstname' => 'Alex',
                'lastname' => 'Toucan',
                'email' => 'a@gmail.com',
                'email_verified_at' => now(),
                'password' => Hash::make('azertyui'), // password
                'remember_token' => Str::random(10),
            ],

            [
                'firstname' => 'Bernard',
                'lastname' => 'Blier',
                'email' => 'b@gmail.com',
                'email_verified_at' => now(),
                'password' => Hash::make('azertyui'), // password
                'remember_token' => Str::random(10),
            ],

            [
                'firstname' => 'Cédric',
                'lastname' => 'Jojo',
                'email' => 'c@gmail.com',
                'email_verified_at' => now(),
                'password' => Hash::make('azertyui'), // password
                'remember_token' => Str::random(10),
            ],

            [
                'firstname' => 'Damien',
                'lastname' => 'Gola',
                'email' => 'd@gmail.com',
                'email_verified_at' => now(),
                'password' => Hash::make('azertyui'), // password
                'remember_token' => Str::random(10),
            ],

            [
                'firstname' => 'Gérard',
                'lastname' => 'Mensoif',
                'email' => 'e@gmail.com',
                'email_verified_at' => now(),
                'password' => Hash::make('azertyui'), // password
                'remember_token' => Str::random(10),
            ],

            [
                'firstname' => 'Harry',
                'lastname' => 'Foly',
                'email' => 'f@gmail.com',
                'email_verified_at' => now(),
                'password' => Hash::make('azertyui'), // password
                'remember_token' => Str::random(10),
            ]
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
