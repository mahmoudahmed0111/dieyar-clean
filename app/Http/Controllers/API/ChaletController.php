<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Chalet;
use App\Http\Controllers\API\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

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
















    /**
     * عرض معلومات الشاليه مع سجل النظافة والمخزون
     */
    public function chaletInfo(Request $request)
    {
        // التحقق من البيانات المطلوبة
        $validator = Validator::make($request->all(), [
            'chalet_id' => 'required|exists:chalets,id',
            'cleaning_type' => 'required|in:regular,deep',
            'cleaning_date' => 'required|date',
        ], [
            'chalet_id.required' => 'معرف الشاليه مطلوب',
            'chalet_id.exists' => 'الشاليه غير موجود',
            'cleaning_type.required' => 'نوع النظافة مطلوب',
            'cleaning_type.in' => 'نوع النظافة يجب أن يكون regular أو deep',
            'cleaning_date.required' => 'تاريخ النظافة مطلوب',
            'cleaning_date.date' => 'تاريخ النظافة غير صحيح',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(null, $validator->errors()->first(), 422);
        }

        $chaletId = $request->chalet_id;
        $cleaningType = $request->cleaning_type;
        $cleaningDate = $request->cleaning_date;

        // جلب بيانات الشاليه
        $chalet = Chalet::with(['images', 'videos'])->find($chaletId);

        // تحديد نوع النظافة
        $cleaningModel = $cleaningType === 'regular' ? 'RegularCleaning' : 'DeepCleaning';
        $cleaningTable = $cleaningType === 'regular' ? 'regular_cleanings' : 'deep_cleanings';

        // البحث عن سجل النظافة في التاريخ المحدد
        $cleaningRecord = DB::table($cleaningTable)
            ->where('chalet_id', $chaletId)
            ->whereDate('created_at', $cleaningDate)
            ->first();

        // جلب الصور والفيديوهات قبل النظافة (الافتراضية من الشاليه)
        $beforeImages = $chalet->images->map(function ($image) {
            return [
                'id' => $image->id,
                'image' => asset('storage/' . $image->image),
                // 'created_at' => $image->created_at->format('Y-m-d H:i:s'),
                // 'source' => 'chalet_original'
            ];
        });

        $beforeVideos = $chalet->videos->map(function ($video) {
            return [
                'id' => $video->id,
                'video' => asset('storage/' . $video->video),
                // 'created_at' => $video->created_at->format('Y-m-d H:i:s'),
                // 'source' => 'chalet_original'
            ];
        });

        // جلب الصور والفيديوهات بعد النظافة
        $afterImages = collect();
        $afterVideos = collect();

        if ($cleaningRecord) {
            // جلب الصور والفيديوهات قبل النظافة من جداول النظافة
            $cleaningBeforeImages = DB::table($cleaningType . '_cleaning_images')
                ->where($cleaningType . '_cleaning_id', $cleaningRecord->id)
                ->where('type', 'before')
                ->get();

            if ($cleaningBeforeImages->count() > 0) {
                $beforeImages = $cleaningBeforeImages->map(function ($image) {
                    return [
                        'id' => $image->id,
                        'image' => asset('storage/' . $image->image),
                        // 'created_at' => $image->created_at,
                        // 'source' => 'cleaning_before'
                    ];
                });
            }

            $cleaningBeforeVideos = DB::table($cleaningType . '_cleaning_videos')
                ->where($cleaningType . '_cleaning_id', $cleaningRecord->id)
                ->where('type', 'before')
                ->get();

            if ($cleaningBeforeVideos->count() > 0) {
                $beforeVideos = $cleaningBeforeVideos->map(function ($video) {
                    return [
                        'id' => $video->id,
                        'video' => asset('storage/' . $video->video),
                        //  'created_at' => $video->created_at,
                        // 'source' => 'cleaning_before'
                    ];
                });
            }

            // جلب الصور والفيديوهات بعد النظافة
            $afterImages = DB::table($cleaningType . '_cleaning_images')
                ->where($cleaningType . '_cleaning_id', $cleaningRecord->id)
                ->where('type', 'after')
                ->get()
                ->map(function ($image) {
                    return [
                        'id' => $image->id,
                        'image' => asset('storage/' . $image->image),
                        // 'created_at' => $image->created_at,
                        // 'source' => 'cleaning_after'
                    ];
                });

            $afterVideos = DB::table($cleaningType . '_cleaning_videos')
                ->where($cleaningType . '_cleaning_id', $cleaningRecord->id)
                ->where('type', 'after')
                ->get()
                ->map(function ($video) {
                    return [
                        'id' => $video->id,
                        'video' => asset('storage/' . $video->video),
                        // 'created_at' => $video->created_at,
                        // 'source' => 'cleaning_after'
                    ];
                });
        }

        // جلب سجل المخزون المستخدم في النظافة
        $inventoryRecords = collect();
        $totalCost = 0;

        if ($cleaningRecord) {
            $inventoryTable = $cleaningType . '_cleaning_inventory';
            $inventoryRecords = DB::table($inventoryTable)
                ->join('inventory', $inventoryTable . '.inventory_id', '=', 'inventory.id')
                ->where($inventoryTable . '.' . $cleaningType . '_cleaning_id', $cleaningRecord->id)
                ->select([
                    'inventory.id',
                    'inventory.name as product_name',
                    'inventory.image',
                    'inventory.price',
                    $inventoryTable . '.quantity as quantity_used',
                    DB::raw('(inventory.price * ' . $inventoryTable . '.quantity) as total_cost')
                ])
                ->get()
                ->map(function ($item) use (&$totalCost) {
                    $item->image = asset('storage/' . $item->image);
                    $item->total_cost = (float) $item->total_cost;
                    $totalCost += $item->total_cost;
                    return $item;
                });
        }

        // تجميع البيانات
        $response = [
            'chalet' => [
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
            ],
            'cleaning_info' => [
                'type' => $cleaningType === 'regular' ? 'نظافة عادية' : 'نظافة عميقة',
                'date' => $cleaningDate,
                'has_record' => (bool) $cleaningRecord,
                'record_id' => $cleaningRecord ? $cleaningRecord->id : null,
            ],
            'media' => [
                'before_cleaning' => [
                    'images' => $beforeImages,
                    'videos' => $beforeVideos,
                ],
                'after_cleaning' => [
                    'images' => $afterImages,
                    'videos' => $afterVideos,
                ],
            ],
            'inventory_used' => [
                'records' => $inventoryRecords,
                'total_cost' => $totalCost,
                'items_count' => $inventoryRecords->count(),
            ],
        ];

        return $this->apiResponse($response, 'تم جلب معلومات الشاليه بنجاح');
    }
}
