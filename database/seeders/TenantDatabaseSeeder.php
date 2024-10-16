<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TenantDatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            CounterSeeder::class,
            CustomAccessRolesSeeder::class,
            AddonsSettingSeeder::class
        ]);
    }
}
