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
                'firstname' => 'Martin',
                'lastname' => 'Luciani',
                'email' => 'm@gmail.com',
                'sex' => 'male',
                'email_verified_at' => now(),
                'password' => Hash::make('martluci'), // password
                'remember_token' => Str::random(10),
            ],

            [
                'firstname' => 'Bernard',
                'lastname' => 'Blier',
                'email' => 'b@gmail.com',
                'sex' => 'male',
                'email_verified_at' => now(),
                'password' => Hash::make('azertyui'), // password
                'remember_token' => Str::random(10),
            ],

            [
                'firstname' => 'Christophe',
                'lastname' => 'Luciani',
                'email' => 'c@gmail.com',
                'sex' => 'male',
                'email_verified_at' => now(),
                'password' => Hash::make('azertyui'), // password
                'remember_token' => Str::random(10),
            ],

            [
                'firstname' => 'Damien',
                'lastname' => 'Gola',
                'email' => 'd@gmail.com',
                'sex' => 'male',
                'email_verified_at' => now(),
                'password' => Hash::make('azertyui'), // password
                'remember_token' => Str::random(10),
            ],

            [
                'firstname' => 'Gérard',
                'lastname' => 'Mensoif',
                'email' => 'e@gmail.com',
                'sex' => 'male',
                'email_verified_at' => now(),
                'password' => Hash::make('azertyui'), // password
                'remember_token' => Str::random(10),
            ],

            [
                'firstname' => 'Calo',
                'lastname' => 'Gero',
                'email' => 'f@gmail.com',
                'sex' => 'male',
                'email_verified_at' => now(),
                'password' => Hash::make('azertyui'), // password
                'remember_token' => Str::random(10),
            ],

            [
                'firstname' => 'Mylène',
                'lastname' => 'Farmer',
                'email' => 'g@gmail.com',
                'sex' => 'female',
                'email_verified_at' => now(),
                'password' => Hash::make('azertyui'), // password
                'remember_token' => Str::random(10),
            ],

            [
                'firstname' => 'Harry',
                'lastname' => 'Foly',
                'email' => 'h@gmail.com',
                'sex' => 'male',
                'email_verified_at' => now(),
                'password' => Hash::make('azertyui'), // password
                'remember_token' => Str::random(10),
            ],

            [
                'firstname' => 'Louis',
                'lastname' => 'De Funes',
                'email' => 'i@gmail.com',
                'sex' => 'male',
                'email_verified_at' => now(),
                'password' => Hash::make('azertyui'), // password
                'remember_token' => Str::random(10),
            ],

            [
                'firstname' => 'Christian',
                'lastname' => 'Clavier',
                'email' => 'j@gmail.com',
                'sex' => 'male',
                'email_verified_at' => now(),
                'password' => Hash::make('azertyui'), // password
                'remember_token' => Str::random(10),
            ],

        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
