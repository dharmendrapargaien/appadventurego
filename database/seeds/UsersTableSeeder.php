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
                'email'      => 'dharmendrapargaien@gmail.com',
                'password'   => bcrypt('@dmin@123'),
                'role_id'    => 1 
            ]);
        }
    }
}
