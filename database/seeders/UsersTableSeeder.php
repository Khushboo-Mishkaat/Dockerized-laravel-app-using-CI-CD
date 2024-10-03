<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class UsersTableSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            [
                'cognitoId' => Str::uuid(),
                'email' => 'john.doe@example.com',
                'password' => bcrypt('test1234@'),
                'userName' => 'john_doe',
                'firstName' => 'John',
                'lastName' => 'Doe',
                'phoneNumber' => '123-456-7890',
                'gender' => 'Male',
                'birthDate' => '1990-01-01',
                'profilePicture' => 'profile1.jpg',
                'subscriptionDate' => '2024-01-01',
                'expirationDate' => '2025-01-01',
                'freeTrial' => 1,
                'isActivated' => 1,
                'isAdmin' => 0,
                'created' => Carbon::now(),
                'updated' => Carbon::now(),
            ],
        ]);
    }
}
