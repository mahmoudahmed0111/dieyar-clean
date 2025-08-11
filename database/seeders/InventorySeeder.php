<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Inventory;

class InventorySeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            [
                'name' => 'منظف أرضيات مركز',
                'price' => '45',
                'quantity' => '20',
                'image' => null,
            ],
            [
                'name' => 'فوط تنظيف ميكروفايبر',
                'price' => '10',
                'quantity' => '100',
                'image' => null,
            ],
            [
                'name' => 'سائل تنظيف زجاج',
                'price' => '25',
                'quantity' => '30',
                'image' => null,
            ],
            [
                'name' => 'جردل بلاستيك',
                'price' => '15',
                'quantity' => '15',
                'image' => null,
            ],
            [
                'name' => 'ممسحة أرضيات',
                'price' => '35',
                'quantity' => '25',
                'image' => null,
            ],
            [
                'name' => 'قفازات مطاطية',
                'price' => '5',
                'quantity' => '200',
                'image' => null,
            ],
            [
                'name' => 'سائل تعقيم',
                'price' => '30',
                'quantity' => '40',
                'image' => null,
            ],
            [
                'name' => 'فرشاة تنظيف',
                'price' => '12',
                'quantity' => '50',
                'image' => null,
            ],
            [
                'name' => 'مناديل ورقية',
                'price' => '8',
                'quantity' => '150',
                'image' => null,
            ],
            [
                'name' => 'سلة مهملات صغيرة',
                'price' => '20',
                'quantity' => '10',
                'image' => null,
            ],
        ];
        foreach ($items as $item) {
            Inventory::create($item);
        }
    }
}
