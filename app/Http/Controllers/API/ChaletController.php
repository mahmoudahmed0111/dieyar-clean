<?php

namespace App\Http\Controllers\API;

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
        $query = Chalet::with(['images']);

        // البحث في جميع الحقول المطلوبة
        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('floor', 'like', "%{$search}%")
                  ->orWhere('building', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // فلترة حسب الحالة
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // فلترة حسب النوع
        if ($request->type) {
            $query->where('type', $request->type);
        }

        // فلترة حسب حالة التنظيف
        if ($request->has('is_cleaned')) {
            $query->where('is_cleaned', $request->is_cleaned);
        }

        // فلترة حسب حالة الحجز
        if ($request->has('is_booked')) {
            $query->where('is_booked', $request->is_booked);
        }

        $chalets = $query->orderBy('created_at', 'desc')
                        ->paginate($request->per_page ?? 15);

        // تنسيق البيانات للرد
        $formattedChalets = $chalets->getCollection()->map(function ($chalet) {
            return [
                'id' => $chalet->id,
                'name' => $chalet->name,
                'code' => $chalet->code,
                'floor' => $chalet->floor ?? null,
                'building' => $chalet->building ?? null,
                'location' => $chalet->location ?? null,
                'description' => $chalet->description ?? null,
                'status' => $chalet->status,
                'type' => $chalet->type ?? null,
                'is_cleaned' => (bool) $chalet->is_cleaned,
                'is_booked' => (bool) $chalet->is_booked,
                'image' => $chalet->images->first() ? asset('storage/' . $chalet->images->first()->image) : null,
                'images_count' => $chalet->images->count(),
                'created_at' => $chalet->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $chalet->updated_at->format('Y-m-d H:i:s'),
            ];
        });

        // إعادة بناء الـ pagination مع البيانات المنسقة
        $chalets->setCollection($formattedChalets);

        return $this->apiResponse([
            'chalets' => $chalets->items(),
            'search_results_count' => $chalets->total(), // عدد العقارات التي تم العثور عليها
            'pagination' => [
                'current_page' => $chalets->currentPage(),
                'last_page' => $chalets->lastPage(),
                'per_page' => $chalets->perPage(),
                'total' => $chalets->total(),
                'from' => $chalets->firstItem(),
                'to' => $chalets->lastItem(),
            ]
        ], 'تم جلب الشاليهات بنجاح');
    }

    /**
     * عرض بيانات شاليه محدد
     */
    public function show(Chalet $chalet)
    {
        $chalet->load(['images', 'videos']);

        $formattedChalet = [
            'id' => $chalet->id,
            'name' => $chalet->name,
            'code' => $chalet->code,
            'floor' => $chalet->floor ?? null,
            'building' => $chalet->building ?? null,
            'location' => $chalet->location ?? null,
            'description' => $chalet->description ?? null,
            'status' => $chalet->status,
            'type' => $chalet->type ?? null,
            'is_cleaned' => (bool) $chalet->is_cleaned,
            'is_booked' => (bool) $chalet->is_booked,
            'images' => $chalet->images->map(function ($image) {
                return [
                    'id' => $image->id,
                    'image' => asset('storage/' . $image->image),
                    'created_at' => $image->created_at->format('Y-m-d H:i:s'),
                ];
            }),
            'videos' => $chalet->videos->map(function ($video) {
                return [
                    'id' => $video->id,
                    'video' => asset('storage/' . $video->video),
                    'created_at' => $video->created_at->format('Y-m-d H:i:s'),
                ];
            }),
            'images_count' => $chalet->images->count(),
            'videos_count' => $chalet->videos->count(),
            'created_at' => $chalet->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $chalet->updated_at->format('Y-m-d H:i:s'),
        ];

        return $this->apiResponse($formattedChalet, 'تم جلب بيانات الشاليه بنجاح');
    }

    /**
     * إحصائيات الشاليهات
     */
    public function stats()
    {
        $stats = [
            'total_chalets' => Chalet::count(),
            'available_chalets' => Chalet::where('status', 'available')->count(),
            'unavailable_chalets' => Chalet::where('status', 'unavailable')->count(),
            'cleaned_chalets' => Chalet::where('is_cleaned', true)->count(),
            'not_cleaned_chalets' => Chalet::where('is_cleaned', false)->count(),
            'booked_chalets' => Chalet::where('is_booked', true)->count(),
            'available_for_booking' => Chalet::where('is_booked', false)->count(),
            'by_type' => [
                'apartment' => Chalet::where('type', 'apartment')->count(),
                'studio' => Chalet::where('type', 'studio')->count(),
                'villa' => Chalet::where('type', 'villa')->count(),
            ]
        ];

        return $this->apiResponse($stats, 'تم جلب إحصائيات الشاليهات بنجاح');
    }
}
