<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(env('APP_ENV') === "local" || env('APP_ENV') === "dev" || env('APP_ENV') === "staging"){

            \App\Models\User::firstOrCreate([
                'name'       => 'dharmendra',
                'email'      => 'david.dharmendra@ithands.net',
                'password'   => '$2y$10$4ns4kUFFaKRDGXm4nwXxB.pwjXqOnD6i5B4B79bKGhVO8vPSOb0Ke',//@dmin@123
            ]);
        }
    }
}
