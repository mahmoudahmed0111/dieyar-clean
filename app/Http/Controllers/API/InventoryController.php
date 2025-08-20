<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Http\Controllers\API\ResponseTrait;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    use ResponseTrait;
    /**
     * عرض قائمة المخزون
     */
    public function index(Request $request)
    {
        $query = Inventory::query();

        // البحث في الاسم
        if ($request->search) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        // فلترة المنتجات منخفضة الكمية (أقل من 3)
        if ($request->low_products == 1) {
            $query->where('quantity', '<', 3);
        }

        // ترتيب النتائج حسب الاسم
        $query->orderBy('name', 'asc');

        // جلب البيانات مع pagination
        $inventory = $query->paginate($request->per_page ?? 15);

        // تنسيق البيانات للرد مع الأعمدة الموجودة في الجدول
        $formattedInventory = $inventory->getCollection()->map(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'price' => (float) $item->price,
                'quantity' => (int) $item->quantity,
                'image' => $item->image ? asset('storage/' . $item->image) : null,
                // 'created_at' => $item->created_at->format('Y-m-d H:i:s'),
                // 'updated_at' => $item->updated_at->format('Y-m-d H:i:s'),
            ];
        });

        // إعادة بناء الـ pagination مع البيانات المنسقة
        $inventory->setCollection($formattedInventory);

        return $this->apiResponse([
            'inventory' => $inventory->items(),
            'search_results_count' => $inventory->total(),
            'pagination' => [
                'current_page' => $inventory->currentPage(),
                'last_page' => $inventory->lastPage(),
                'per_page' => $inventory->perPage(),
                'total' => $inventory->total(),
                'from' => $inventory->firstItem(),
                'to' => $inventory->lastItem(),
            ]
        ], 'تم جلب المخزون بنجاح');
    }

    /**
     * عرض بيانات عنصر مخزون محدد
     */
    public function show(Inventory $inventory)
    {
        $formattedInventory = [
            'id' => $inventory->id,
            'name' => $inventory->name,
            'price' => (float) $inventory->price,
            'quantity' => (int) $inventory->quantity,
            'image' => $inventory->image ? asset('storage/' . $inventory->image) : null,
            'created_at' => $inventory->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $inventory->updated_at->format('Y-m-d H:i:s'),
        ];

        return $this->apiResponse($formattedInventory, 'تم جلب بيانات العنصر بنجاح');
    }
}
