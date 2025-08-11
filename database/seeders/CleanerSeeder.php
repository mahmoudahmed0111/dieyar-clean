<?php

namespace Database\Seeders;

use App\Models\Cleaner;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;


class CleanerSeeder extends Seeder
{
    public function run(): void
    {
        $cleaners = [
            [
                'name' => 'أحمد محمد',
                'phone' => '+966501234567',
                'email' => 'ahmed@example.com',
                'password' => Hash::make('12345678'),
                'national_id' => '1234567890',
                'address' => 'الرياض، المملكة العربية السعودية',
                'hire_date' => '2023-01-15',
                'status' => 'active',
                'image' => null,
            ],
            [
                'name' => 'فاطمة علي',
                'phone' => '+966507654321',
                'email' => 'fatima@example.com',
                'password' => Hash::make('12345678'),
                'national_id' => '0987654321',
                'address' => 'جدة، المملكة العربية السعودية',
                'hire_date' => '2023-03-20',
                'status' => 'active',
                'image' => null,
            ],
            [
                'name' => 'محمد عبدالله',
                'phone' => '+966509876543',
                'email' => 'mohammed@example.com',
                'password' => Hash::make('12345678'),
                'national_id' => '1122334455',
                'address' => 'الدمام، المملكة العربية السعودية',
                'hire_date' => '2023-06-10',
                'status' => 'active',
                'image' => null,
            ],
        ];

        foreach ($cleaners as $cleaner) {
            Cleaner::create($cleaner);
        }
    }
}
