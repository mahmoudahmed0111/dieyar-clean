<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChaletSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('chalets')->insert([
            [
                'name' => 'Chalet A',
                'location' => 'North Coast',
                'description' => 'Sea view, 2 bedrooms',
                'status' => 'available',
                'type' => 'apartment',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Chalet B',
                'location' => 'Ain Sokhna',
                'description' => 'Pool access, 1 bedroom',
                'status' => 'unavailable',
                'type' => 'studio',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Chalet C',
                'location' => 'Hurghada',
                'description' => 'Garden, 3 bedrooms',
                'status' => 'available',
                'type' => 'villa',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
