<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(UserSeeder::class);
        $this->call(SettingsTableSeeder::class);
        $this->call(CleanerSeeder::class);
        $this->call(ChaletSeeder::class);
        $this->call(InventorySeeder::class);
    }
}
