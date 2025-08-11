<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('settings')->insert([
            'name' => 'Kareem Clean',
            'phone' => '+20123456789',
            'email' => 'info@kareemclean.com',
            'address' => 'Cairo, Egypt',
            'logo' => null,
            'facebook' => 'https://facebook.com/kareemclean',
            'instagram' => 'https://instagram.com/kareemclean',
            'twitter' => 'https://twitter.com/kareemclean',
            'whatsapp' => '+20123456789',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
