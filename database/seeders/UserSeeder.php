<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
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
                'email' => 'admin@kingsgroup.ae',
                'user_type'=>'employee',
                'designation'=>'Super User',
                'email_verified_at' => null,
                'password' => bcrypt('aamir!!@@3'), // password
                'remember_token' => Str::random(10),
                'type'=>'su',
                'props'=>[],
                'status'=>1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];
        if(\DB::getCollection('users')->count() == 0)
        {
           \DB::getCollection('users')->insertMany($admins);
        }

    }
}
