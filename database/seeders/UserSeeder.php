<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // إنشاء مستخدم أدمن رئيسي
        User::create([
            'name' => 'مدير النظام',
            'email' => 'info@gmail.com',
            'password' => Hash::make('12345678'),
            'email_verified_at' => now(),
        ]);
    }
}
