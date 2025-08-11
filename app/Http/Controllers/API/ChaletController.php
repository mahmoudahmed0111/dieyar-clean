<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Chalet;
use App\Http\Controllers\API\ResponseTrait;
use Illuminate\Http\Request;

class ChaletController extends Controller
{
    use ResponseTrait;
    /**
     * عرض قائمة الشاليهات
     */
    public function index(Request $request)
    {
        $chalets = Chalet::with(['images', 'videos'])
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('location', 'like', "%{$search}%");
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->type, function ($query, $type) {
                $query->where('type', $type);
            })
            ->paginate($request->per_page ?? 15);

        return $this->apiResponse($chalets, 'تم جلب الشاليهات بنجاح');
    }

    /**
     * عرض بيانات شاليه محدد
     */
    public function show(Chalet $chalet)
    {
        $chalet->load(['images', 'videos']);

        return $this->apiResponse($chalet, 'تم جلب بيانات الشاليه بنجاح');
    }
}
