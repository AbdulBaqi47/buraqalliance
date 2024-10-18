<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
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
        $admins = [
            [
                'name' => 'Admin',
                'email' => 'admin@buraqalliance.com',
                'user_type' => 'employee',
                'designation' => 'Super User',
                'email_verified_at' => null,
                'password' => bcrypt('buraq!!@@47'), // password
                'remember_token' => Str::random(10),
                'type' => 'su',
                'props' => json_encode([]), // Store as an empty JSON array
                'status' => 1,
                'created_at' => now(), // Use now() for the timestamp
                'updated_at' => now(),
            ]
        ];

        if (DB::table('users')->count() == 0) {
            DB::table('users')->insert($admins);
        }
    }
}
