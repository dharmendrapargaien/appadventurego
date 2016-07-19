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
        if(env('APP_ENV') !== "production"){

            \App\Models\User::firstOrCreate([
                'name'       => 'dharmendra',
                'email'      => 'david.dharmendra@ithands.net',
                'password'   => '$2y$10$4ns4kUFFaKRDGXm4nwXxB.pwjXqOnD6i5B4B79bKGhVO8vPSOb0Ke',//@dmin@123
                'role_id'    => 1 
            ]);
        }
    }
}
