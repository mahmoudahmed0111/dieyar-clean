<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Chalet;
use App\Models\Damage;
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
                    ->orWhere('pass_code', 'like', "%{$search}%")
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
                'pass_code' => $chalet->pass_code,
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
            'pass_code' => $chalet->pass_code,
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
            'media_type' => 'required|in:before,after',
        ], [
            'chalet_id.required' => 'معرف الشاليه مطلوب',
            'chalet_id.exists' => 'الشاليه غير موجود',
            'cleaning_type.required' => 'نوع النظافة مطلوب',
            'cleaning_type.in' => 'نوع النظافة يجب أن يكون regular أو deep',
            'cleaning_date.required' => 'تاريخ النظافة مطلوب',
            'cleaning_date.date' => 'تاريخ النظافة غير صحيح',
            'media_type.required' => 'نوع الوسائط مطلوب',
            'media_type.in' => 'نوع الوسائط يجب أن يكون before أو after',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(null, $validator->errors()->first(), 422);
        }

        $chaletId = $request->chalet_id;
        $cleaningType = $request->cleaning_type;
        $cleaningDate = $request->cleaning_date;
        $mediaType = $request->media_type;

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

        // تهيئة المتغيرات للوسائط
        $images = collect();
        $videos = collect();

        if ($mediaType === 'before') {
            // جلب الصور والفيديوهات قبل النظافة (الافتراضية من الشاليه)
            $images = $chalet->images->map(function ($image) {
                return [
                    'id' => $image->id,
                    'image' => asset('storage/' . $image->image),
                ];
            });

            $videos = $chalet->videos->map(function ($video) {
                return [
                    'id' => $video->id,
                    'video' => asset('storage/' . $video->video),
                ];
            });

            // إذا وجد سجل نظافة، جلب الصور والفيديوهات قبل النظافة من جداول النظافة
            if ($cleaningRecord) {
                $cleaningBeforeImages = DB::table($cleaningType . '_cleaning_images')
                    ->where($cleaningType . '_cleaning_id', $cleaningRecord->id)
                    ->where('type', 'before')
                    ->get();

                if ($cleaningBeforeImages->count() > 0) {
                    $images = $cleaningBeforeImages->map(function ($image) {
                        return [
                            'id' => $image->id,
                            'image' => asset('storage/' . $image->image),
                        ];
                    });
                }

                $cleaningBeforeVideos = DB::table($cleaningType . '_cleaning_videos')
                    ->where($cleaningType . '_cleaning_id', $cleaningRecord->id)
                    ->where('type', 'before')
                    ->get();

                if ($cleaningBeforeVideos->count() > 0) {
                    $videos = $cleaningBeforeVideos->map(function ($video) {
                        return [
                            'id' => $video->id,
                            'video' => asset('storage/' . $video->video),
                        ];
                    });
                }
            }
        } else {
            // جلب الصور والفيديوهات بعد النظافة
            if ($cleaningRecord) {
                $images = DB::table($cleaningType . '_cleaning_images')
                    ->where($cleaningType . '_cleaning_id', $cleaningRecord->id)
                    ->where('type', 'after')
                    ->get()
                    ->map(function ($image) {
                        return [
                            'id' => $image->id,
                            'image' => asset('storage/' . $image->image),
                        ];
                    });

                $videos = DB::table($cleaningType . '_cleaning_videos')
                    ->where($cleaningType . '_cleaning_id', $cleaningRecord->id)
                    ->where('type', 'after')
                    ->get()
                    ->map(function ($video) {
                        return [
                            'id' => $video->id,
                            'video' => asset('storage/' . $video->video),
                        ];
                    });
            }
        }

        // جلب سجل المخزون المستخدم في النظافة (فقط في حالة after)
        $inventoryRecords = collect();
        $totalCost = 0;

        if ($cleaningRecord && $mediaType === 'after') {
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
                'pass_code' => $chalet->pass_code,
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
                'type' => $mediaType === 'before' ? 'قبل النظافة' : 'بعد النظافة',
                'images' => $images,
                'videos' => $videos,
            ],
        ];

        // إضافة المخزون فقط في حالة after
        if ($mediaType === 'after') {
            $response['inventory_used'] = [
                'records' => $inventoryRecords,
                'total_cost' => $totalCost,
                'items_count' => $inventoryRecords->count(),
            ];
        }

        return $this->apiResponse($response, 'تم جلب معلومات الشاليه بنجاح');
    }

    /**
     * عرض معلومات الخدمات (الصيانة، المكافحة) للشاليه
     */
    public function serviceInfo(Request $request)
    {
        // التحقق من البيانات المطلوبة
        $validator = Validator::make($request->all(), [
            'chalet_id' => 'required|exists:chalets,id',
            'type' => 'required|in:maintenance,pest_control',
            'date' => 'required|date',
            'media_type' => 'required|in:before,after',
        ], [
            'chalet_id.required' => 'معرف الشاليه مطلوب',
            'chalet_id.exists' => 'الشاليه غير موجود',
            'type.required' => 'نوع الخدمة مطلوب',
            'type.in' => 'نوع الخدمة يجب أن يكون maintenance أو pest_control',
            'date.required' => 'تاريخ الخدمة مطلوب',
            'date.date' => 'تاريخ الخدمة غير صحيح',
            'media_type.required' => 'نوع الوسائط مطلوب',
            'media_type.in' => 'نوع الوسائط يجب أن يكون before أو after',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(null, $validator->errors()->first(), 422);
        }

        $chaletId = $request->chalet_id;
        $serviceType = $request->type;
        $serviceDate = $request->date;
        $mediaType = $request->media_type;

        // جلب بيانات الشاليه
        $chalet = Chalet::find($chaletId);

        // تحديد نوع الخدمة والجداول
        $serviceTable = $this->getServiceTable($serviceType);
        $imageTable = $this->getImageTable($serviceType);
        $videoTable = $this->getVideoTable($serviceType);

        // البحث عن سجل الخدمة في التاريخ المحدد
        $serviceRecord = DB::table($serviceTable)
            ->where('chalet_id', $chaletId)
            ->whereDate('created_at', $serviceDate)
            ->orderBy('created_at', 'desc')
            ->first();

        // تهيئة المتغيرات للوسائط
        $images = collect();
        $videos = collect();

        if ($serviceRecord) {
            // Debug: Log service record info
            \Log::info('Service record found:', [
                'id' => $serviceRecord->id,
                'chalet_id' => $serviceRecord->chalet_id,
                'service_type' => $serviceType,
                'table' => $serviceTable,
            ]);

            // جلب الصور
            $images = DB::table($imageTable)
                ->where($this->getServiceIdColumn($serviceType), $serviceRecord->id)
                ->get()
                ->map(function ($image) {
                    return [
                        'id' => $image->id,
                        'image' => asset('storage/' . $image->image),
                        'created_at' => $image->created_at,
                    ];
                });

            // جلب الفيديوهات
            $videos = DB::table($videoTable)
                ->where($this->getServiceIdColumn($serviceType), $serviceRecord->id)
                ->get()
                ->map(function ($video) {
                    return [
                        'id' => $video->id,
                        'video' => asset('storage/' . $video->video),
                        'created_at' => $video->created_at,
                    ];
                });

            // Debug: Log media info
            \Log::info('Service media data:', [
                'images_count' => $images->count(),
                'videos_count' => $videos->count(),
                'media_type' => $mediaType,
            ]);
        } else {
            // Check if there are any services for this chalet at all
            $allServices = DB::table($serviceTable)->where('chalet_id', $chaletId)->get();
            \Log::info('No service record found for chalet_id: ' . $chaletId . ' and type: ' . $serviceType . ' and date: ' . $serviceDate);
            \Log::info('Total services for chalet_id ' . $chaletId . ': ' . $allServices->count());
            if ($allServices->count() > 0) {
                \Log::info('Available service dates:', $allServices->pluck('created_at')->map(function($date) {
                    return \Carbon\Carbon::parse($date)->format('Y-m-d');
                })->toArray());
            }
        }

        // تجميع البيانات
        $response = [
            'chalet' => [
                'id' => $chalet->id,
                'name' => $chalet->name,
                'pass_code' => $chalet->pass_code,
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
            'service_info' => [
                'type' => $this->getServiceTypeName($serviceType),
                'date' => $serviceDate,
                'has_record' => (bool) $serviceRecord,
                'record_id' => $serviceRecord ? $serviceRecord->id : null,
            ],
            'media' => [
                'type' => $mediaType === 'before' ? 'قبل الخدمة' : 'بعد الخدمة',
                'images' => $images,
                'videos' => $videos,
            ],
        ];

        // إضافة تفاصيل الخدمة إذا وجدت
        if ($serviceRecord) {
            $response['service_details'] = [
                'id' => $serviceRecord->id,
                'description' => $serviceRecord->description ?? null,
                'status' => $serviceRecord->status,
                'price' => $serviceRecord->price ?? null,
                'notes' => $serviceRecord->notes ?? null,
                'created_at' => $serviceRecord->created_at,
                'updated_at' => $serviceRecord->updated_at,
            ];
        }

        return $this->apiResponse($response, 'تم جلب معلومات الخدمة بنجاح');
    }

    /**
     * عرض معلومات التلفيات للشاليه
     */
    public function damageInfo(Request $request)
    {
        // التحقق من البيانات المطلوبة
        $validator = Validator::make($request->all(), [
            'chalet_id' => 'required|exists:chalets,id',
            'date' => 'required|date',
        ], [
            'chalet_id.required' => 'معرف الشاليه مطلوب',
            'chalet_id.exists' => 'الشاليه غير موجود',
            'date.required' => 'تاريخ التلفيات مطلوب',
            'date.date' => 'تاريخ التلفيات غير صحيح',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(null, $validator->errors()->first(), 422);
        }

        $chaletId = $request->chalet_id;
        $damageDate = $request->date;

        // جلب بيانات الشاليه
        $chalet = Chalet::find($chaletId);

        // البحث عن سجل التلفيات في التاريخ المحدد
        $damageRecord = Damage::where('chalet_id', $chaletId)
            ->whereDate('created_at', $damageDate)
            ->with(['images', 'videos'])
            ->orderBy('created_at', 'desc')
            ->first();

        // تهيئة المتغيرات للوسائط
        $images = collect();
        $videos = collect();

        if ($damageRecord) {
            // Debug: Log damage record info
            \Log::info('Damage record found:', [
                'id' => $damageRecord->id,
                'chalet_id' => $damageRecord->chalet_id,
                'images_count' => $damageRecord->images->count(),
                'videos_count' => $damageRecord->videos->count(),
            ]);

            // جلب الصور
            $images = $damageRecord->images->map(function ($image) {
                return [
                    'id' => $image->id,
                    'image' => asset('storage/' . $image->image),
                    'created_at' => $image->created_at,
                ];
            });

            // جلب الفيديوهات
            $videos = $damageRecord->videos->map(function ($video) {
                return [
                    'id' => $video->id,
                    'video' => asset('storage/' . $video->video),
                    'created_at' => $video->created_at,
                ];
            });

            // Debug: Log media info
            \Log::info('Media data:', [
                'images' => $images->toArray(),
                'videos' => $videos->toArray(),
            ]);
        } else {
            // Check if there are any damages for this chalet at all
            $allDamages = Damage::where('chalet_id', $chaletId)->get();
            \Log::info('No damage record found for chalet_id: ' . $chaletId . ' and date: ' . $damageDate);
            \Log::info('Total damages for chalet_id ' . $chaletId . ': ' . $allDamages->count());
            if ($allDamages->count() > 0) {
                \Log::info('Available damage dates:', $allDamages->pluck('created_at')->map(function($date) {
                    return $date->format('Y-m-d');
                })->toArray());
            }
        }

        // تجميع البيانات
        $response = [
            'chalet' => [
                'id' => $chalet->id,
                'name' => $chalet->name,
                'pass_code' => $chalet->pass_code,
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
            'damage_info' => [
                'type' => 'تقرير تلفيات',
                'date' => $damageDate,
                'has_record' => (bool) $damageRecord,
                'record_id' => $damageRecord ? $damageRecord->id : null,
            ],
            'media' => [
                'images' => $images,
                'videos' => $videos,
            ],
        ];

        // إضافة تفاصيل التلفيات إذا وجدت
        if ($damageRecord) {
            $response['damage_details'] = [
                'id' => $damageRecord->id,
                'description' => $damageRecord->description ?? null,
                'status' => $damageRecord->status,
                'price' => $damageRecord->price ?? null,
                'reported_at' => $damageRecord->reported_at,
                'created_at' => $damageRecord->created_at,
                'updated_at' => $damageRecord->updated_at,
            ];
        }

        return $this->apiResponse($response, 'تم جلب معلومات التلفيات بنجاح');
    }

    /**
     * الحصول على اسم جدول الخدمة
     */
    private function getServiceTable($serviceType)
    {
        switch ($serviceType) {
            case 'maintenance':
                return 'maintenance';
            case 'pest_control':
                return 'pest_controls';
            default:
                return 'maintenance';
        }
    }

    /**
     * الحصول على اسم جدول الصور
     */
    private function getImageTable($serviceType)
    {
        switch ($serviceType) {
            case 'maintenance':
                return 'maintenance_images';
            case 'pest_control':
                return 'pest_control_images';
            default:
                return 'maintenance_images';
        }
    }

    /**
     * الحصول على اسم جدول الفيديوهات
     */
    private function getVideoTable($serviceType)
    {
        switch ($serviceType) {
            case 'maintenance':
                return 'maintenance_videos';
            case 'pest_control':
                return 'pest_control_videos';
            default:
                return 'maintenance_videos';
        }
    }

    /**
     * الحصول على اسم عمود معرف الخدمة
     */
    private function getServiceIdColumn($serviceType)
    {
        switch ($serviceType) {
            case 'maintenance':
                return 'maintenance_id';
            case 'pest_control':
                return 'pest_control_id';
            default:
                return 'maintenance_id';
        }
    }

    /**
     * الحصول على اسم نوع الخدمة بالعربية
     */
    private function getServiceTypeName($serviceType)
    {
        switch ($serviceType) {
            case 'maintenance':
                return 'طلب صيانة';
            case 'pest_control':
                return 'مكافحة حشرات';
            default:
                return 'خدمة';
        }
    }
}
