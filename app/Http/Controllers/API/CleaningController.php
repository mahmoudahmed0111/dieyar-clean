<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Chalet;
use App\Models\Cleaner;
use App\Models\RegularCleaning;
use App\Models\DeepCleaning;
use App\Models\RegularCleaningImage;
use App\Models\DeepCleaningImage;
use App\Models\RegularCleaningVideo;
use App\Models\DeepCleaningVideo;
use App\Models\Inventory;
use App\Http\Controllers\API\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class CleaningController extends Controller
{
    use ResponseTrait;

    /**
     * رفع النظافة (قبل أو بعد)
     */
    public function uploadCleaning(Request $request)
    {
        try {
            // معالجة inventory_items إذا كانت نص JSON
            if ($request->has('inventory_items') && is_string($request->inventory_items)) {
                $inventoryItems = json_decode($request->inventory_items, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $request->merge(['inventory_items' => $inventoryItems]);
                } else {
                    // إذا كان JSON غير صحيح، إرجاع خطأ واضح
                    return $this->apiResponse(null, 'تنسيق المنتجات غير صحيح. يجب أن يكون JSON صحيح مثل: [{"inventory_id":1,"quantity":2}]', 422);
                }
            } elseif ($request->has('inventory_items') && !is_array($request->inventory_items)) {
                // إذا كانت inventory_items موجودة ولكن ليست مصفوفة ولا نص JSON
                return $this->apiResponse(null, 'المنتجات يجب أن تكون مصفوفة أو JSON صحيح', 422);
            }

            // التحقق من البيانات الأساسية
            $validator = Validator::make($request->all(), [
                'cleaning_type' => 'required|in:deep,regular',
                'chalet_id' => 'required|exists:chalets,id',
                'cleaning_time' => 'required|in:before,after',
                'date' => 'required|date',
                'cleaning_cost' => 'required_if:cleaning_time,after|numeric|min:0',
                'inventory_items' => 'required_if:cleaning_time,after|array',
                'inventory_items.*.inventory_id' => 'required_if:cleaning_time,after|exists:inventory,id',
                'inventory_items.*.quantity' => 'required_if:cleaning_time,after|integer|min:1',
                'images' => 'nullable|array',
                'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240', // 10MB max
                'videos' => 'nullable|array',
                'videos.*' => 'nullable|mimes:mp4,avi,mov,wmv,webm|max:102400', // 100MB max
            ], [
                'cleaning_type.required' => 'نوع النظافة مطلوب',
                'cleaning_type.in' => 'نوع النظافة يجب أن يكون deep أو regular',
                'chalet_id.required' => 'معرف الشاليه مطلوب',
                'chalet_id.exists' => 'الشاليه غير موجود',
                'cleaning_time.required' => 'وقت النظافة مطلوب',
                'cleaning_time.in' => 'وقت النظافة يجب أن يكون before أو after',
                'date.required' => 'تاريخ النظافة مطلوب',
                'date.date' => 'تاريخ النظافة غير صحيح',
                'cleaning_cost.required_if' => 'سعر النظافة مطلوب في حالة after',
                'cleaning_cost.numeric' => 'سعر النظافة يجب أن يكون رقم',
                'cleaning_cost.min' => 'سعر النظافة يجب أن يكون أكبر من صفر',
                'inventory_items.required_if' => 'المنتجات المستخدمة مطلوبة في حالة after',
                'inventory_items.array' => 'المنتجات يجب أن تكون مصفوفة',
                'inventory_items.*.inventory_id.required_if' => 'معرف المنتج مطلوب',
                'inventory_items.*.inventory_id.exists' => 'المنتج غير موجود',
                'inventory_items.*.quantity.required_if' => 'كمية المنتج مطلوبة',
                'inventory_items.*.quantity.integer' => 'كمية المنتج يجب أن تكون رقم صحيح',
                'inventory_items.*.quantity.min' => 'كمية المنتج يجب أن تكون أكبر من صفر',
                'images.array' => 'الصور يجب أن تكون مصفوفة',
                'images.*.image' => 'الملف يجب أن يكون صورة',
                'images.*.mimes' => 'نوع الصورة غير مدعوم',
                'images.*.max' => 'حجم الصورة يجب أن يكون أقل من 10 ميجابايت',
                'videos.array' => 'الفيديوهات يجب أن تكون مصفوفة',
                'videos.*.mimes' => 'نوع الفيديو غير مدعوم',
                'videos.*.max' => 'حجم الفيديو يجب أن يكون أقل من 100 ميجابايت',
            ]);

            if ($validator->fails()) {
                return $this->apiResponse(null, $validator->errors()->first(), 422);
            }

            $cleaner = $request->user();
            $cleaningType = $request->cleaning_type;
            $cleaningTime = $request->cleaning_time;
            $chaletId = $request->chalet_id;

            // تحديد نموذج النظافة
            $cleaningModel = $cleaningType === 'regular' ? RegularCleaning::class : DeepCleaning::class;
            $imageModel = $cleaningType === 'regular' ? RegularCleaningImage::class : DeepCleaningImage::class;
            $videoModel = $cleaningType === 'regular' ? RegularCleaningVideo::class : DeepCleaningVideo::class;

            // البحث عن سجل النظافة الموجود أو إنشاء جديد
            $cleaningRecord = $cleaningModel::where('chalet_id', $chaletId)
                ->where('cleaner_id', $cleaner->id)
                ->whereDate('date', $request->date)
                ->first();

            if (!$cleaningRecord) {
                // إنشاء سجل نظافة جديد
                $cleaningData = [
                    'chalet_id' => $chaletId,
                    'cleaner_id' => $cleaner->id,
                    'date' => $request->date,
                    'status' => 'completed',
                ];

                if ($cleaningType === 'deep') {
                    $cleaningData['cleaning_type'] = 'deep_cleaning';
                } else {
                    $cleaningData['cleaning_type'] = 'regular_cleaning';
                }

                $cleaningRecord = $cleaningModel::create($cleaningData);
            } else {
                // تحديث السجل الموجود
                $cleaningRecord->update(['status' => 'completed']);
            }

            // رفع الصور
            $uploadedImages = [];
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $path = $image->store($cleaningType . '-cleanings/images', 'public');

                    $uploadedImage = $imageModel::create([
                        $cleaningType . '_cleaning_id' => $cleaningRecord->id,
                        'image' => $path,
                        'type' => $cleaningTime,
                    ]);

                    $uploadedImages[] = [
                        'id' => $uploadedImage->id,
                        'image' => asset('storage/' . $path),
                        'type' => $cleaningTime
                    ];
                }
            }

            // رفع الفيديوهات
            $uploadedVideos = [];
            if ($request->hasFile('videos')) {
                foreach ($request->file('videos') as $video) {
                    $path = $video->store($cleaningType . '-cleanings/videos', 'public');

                    $uploadedVideo = $videoModel::create([
                        $cleaningType . '_cleaning_id' => $cleaningRecord->id,
                        'video' => $path,
                        'type' => $cleaningTime,
                    ]);

                    $uploadedVideos[] = [
                        'id' => $uploadedVideo->id,
                        'video' => asset('storage/' . $path),
                        'type' => $cleaningTime
                    ];
                }
            }

            // معالجة المنتجات المستخدمة (فقط في حالة after)
            $inventoryItems = [];
            if ($cleaningTime === 'after' && $request->inventory_items) {
                $inventoryTable = $cleaningType . '_cleaning_inventory';

                foreach ($request->inventory_items as $item) {
                    $inventory = Inventory::find($item['inventory_id']);

                    if ($inventory && $inventory->quantity >= $item['quantity']) {
                        // إضافة المنتج إلى سجل النظافة
                        DB::table($inventoryTable)->insert([
                            $cleaningType . '_cleaning_id' => $cleaningRecord->id,
                            'inventory_id' => $item['inventory_id'],
                            'quantity' => $item['quantity'],
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                        // تحديث كمية المخزون
                        $inventory->decrement('quantity', $item['quantity']);

                        $inventoryItems[] = [
                            'id' => $inventory->id,
                            'name' => $inventory->name,
                            'quantity_used' => $item['quantity'],
                            'price' => $inventory->price,
                            'total_cost' => $inventory->price * $item['quantity']
                        ];
                    }
                }
            }

            // تحديث سعر النظافة (فقط في حالة after)
            if ($cleaningTime === 'after' && $request->cleaning_cost) {
                $cleaningRecord->update(['price' => $request->cleaning_cost]);
            }

            // تجميع البيانات للرد
            $response = [
                'cleaning_record' => [
                    'id' => $cleaningRecord->id,
                    'chalet_id' => $cleaningRecord->chalet_id,
                    'cleaner_id' => $cleaningRecord->cleaner_id,
                    'date' => $cleaningRecord->date,
                    'cleaning_cost' => $cleaningRecord->price ?? 0,
                    'status' => $cleaningRecord->status,
                ],
                'uploaded_media' => [
                    'images' => $uploadedImages,
                    'videos' => $uploadedVideos,
                    'images_count' => count($uploadedImages),
                    'videos_count' => count($uploadedVideos),
                ],
                'cleaning_info' => [
                    'type' => $cleaningType === 'regular' ? 'نظافة عادية' : 'نظافة عميقة',
                    'time' => $cleaningTime === 'before' ? 'قبل النظافة' : 'بعد النظافة',
                ]
            ];

            // إضافة المنتجات المستخدمة للرد
            if ($cleaningTime === 'after') {
                $response['inventory_used'] = [
                    'items' => $inventoryItems,
                    'total_cost' => array_sum(array_column($inventoryItems, 'total_cost')),
                    'items_count' => count($inventoryItems),
                ];
            }

            $message = 'تم رفع ' . ($cleaningTime === 'before' ? 'الصور والفيديوهات قبل' : 'الصور والفيديوهات بعد') . ' النظافة بنجاح';

            return $this->apiResponse($response, $message, 201);

        } catch (\Exception $e) {
            Log::error('Error in uploadCleaning: ' . $e->getMessage());
            return $this->apiResponse(null, 'حدث خطأ أثناء رفع النظافة: ' . $e->getMessage(), 500);
        }
    }

    /**
     * جلب سجلات النظافة لعامل النظافة
     */
    public function getCleaningHistory(Request $request)
    {
        try {
            $cleaner = $request->user();

            $validator = Validator::make($request->all(), [
                'cleaning_type' => 'nullable|in:deep,regular',
                'chalet_id' => 'nullable|exists:chalets,id',
                'date_from' => 'nullable|date',
                'date_to' => 'nullable|date|after_or_equal:date_from',
                'per_page' => 'nullable|integer|min:1|max:100',
            ]);

            if ($validator->fails()) {
                return $this->apiResponse(null, $validator->errors()->first(), 422);
            }

            $query = DB::table('regular_cleanings')
                ->select([
                    'id',
                    'chalet_id',
                    'cleaner_id',
                    'date',
                    'price as cleaning_cost',
                    'status',
                    DB::raw("'regular' as cleaning_type")
                ])
                ->where('cleaner_id', $cleaner->id);

            if ($request->cleaning_type === 'deep') {
                $query = DB::table('deep_cleanings')
                    ->select([
                        'id',
                        'chalet_id',
                        'cleaner_id',
                        'date',
                        'price as cleaning_cost',
                        'status',
                        DB::raw("'deep' as cleaning_type")
                    ])
                    ->where('cleaner_id', $cleaner->id);
            } elseif (!$request->cleaning_type) {
                // دمج النوعين
                $regularQuery = DB::table('regular_cleanings')
                    ->select([
                        'id',
                        'chalet_id',
                        'cleaner_id',
                        'date',
                        'price as cleaning_cost',
                        'status',
                        DB::raw("'regular' as cleaning_type")
                    ])
                    ->where('cleaner_id', $cleaner->id);

                $deepQuery = DB::table('deep_cleanings')
                    ->select([
                        'id',
                        'chalet_id',
                        'cleaner_id',
                        'date',
                        'price as cleaning_cost',
                        'status',
                        DB::raw("'deep' as cleaning_type")
                    ])
                    ->where('cleaner_id', $cleaner->id);

                $query = $regularQuery->union($deepQuery);
            }

            // تطبيق الفلاتر
            if ($request->chalet_id) {
                $query->where('chalet_id', $request->chalet_id);
            }

            if ($request->date_from) {
                $query->whereDate('date', '>=', $request->date_from);
            }

            if ($request->date_to) {
                $query->whereDate('date', '<=', $request->date_to);
            }

            $perPage = $request->per_page ?? 15;
            $history = $query->orderBy('date', 'desc')
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);

            // إضافة معلومات الشاليه
            $history->getCollection()->transform(function ($item) {
                $chalet = Chalet::find($item->chalet_id);
                $item->chalet = $chalet ? [
                    'id' => $chalet->id,
                    'name' => $chalet->name,
                    'code' => $chalet->code,
                    'pass_code' => $chalet->pass_code,
                ] : null;

                return $item;
            });

            return $this->apiResponse([
                'history' => $history->items(),
                'pagination' => [
                    'current_page' => $history->currentPage(),
                    'last_page' => $history->lastPage(),
                    'per_page' => $history->perPage(),
                    'total' => $history->total(),
                    'from' => $history->firstItem(),
                    'to' => $history->lastItem(),
                ]
            ], 'تم جلب سجل النظافة بنجاح');

        } catch (\Exception $e) {
            Log::error('Error in getCleaningHistory: ' . $e->getMessage());
            return $this->apiResponse(null, 'حدث خطأ أثناء جلب سجل النظافة', 500);
        }
    }

    /**
     * جلب تفاصيل سجل نظافة محدد
     */
    public function getCleaningDetails(Request $request, $id)
    {
        try {
            $cleaner = $request->user();

            $validator = Validator::make($request->all(), [
                'cleaning_type' => 'required|in:deep,regular',
            ]);

            if ($validator->fails()) {
                return $this->apiResponse(null, $validator->errors()->first(), 422);
            }

            $cleaningType = $request->cleaning_type;
            $cleaningTable = $cleaningType . '_cleanings';
            $imageTable = $cleaningType . '_cleaning_images';
            $videoTable = $cleaningType . '_cleaning_videos';
            $inventoryTable = $cleaningType . '_cleaning_inventory';

            // جلب سجل النظافة
            $cleaningRecord = DB::table($cleaningTable)
                ->where('id', $id)
                ->where('cleaner_id', $cleaner->id)
                ->first();

            if (!$cleaningRecord) {
                return $this->apiResponse(null, 'سجل النظافة غير موجود', 404);
            }

            // جلب الصور
            $images = DB::table($imageTable)
                ->where($cleaningType . '_cleaning_id', $id)
                ->get()
                ->map(function ($image) {
                    return [
                        'id' => $image->id,
                        'image' => asset('storage/' . $image->image),
                        'type' => $image->type,
                        'created_at' => $image->created_at,
                    ];
                });

            // جلب الفيديوهات
            $videos = DB::table($videoTable)
                ->where($cleaningType . '_cleaning_id', $id)
                ->get()
                ->map(function ($video) {
                    return [
                        'id' => $video->id,
                        'video' => asset('storage/' . $video->video),
                        'type' => $video->type,
                        'created_at' => $video->created_at,
                    ];
                });

            // جلب المنتجات المستخدمة
            $inventoryItems = DB::table($inventoryTable)
                ->join('inventory', $inventoryTable . '.inventory_id', '=', 'inventory.id')
                ->where($inventoryTable . '.' . $cleaningType . '_cleaning_id', $id)
                ->select([
                    'inventory.id',
                    'inventory.name',
                    'inventory.image',
                    'inventory.price',
                    $inventoryTable . '.quantity as quantity_used',
                    DB::raw('(inventory.price * ' . $inventoryTable . '.quantity) as total_cost')
                ])
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'name' => $item->name,
                        'image' => asset('storage/' . $item->image),
                        'price' => (float) $item->price,
                        'quantity_used' => (int) $item->quantity_used,
                        'total_cost' => (float) $item->total_cost,
                    ];
                });

            // معلومات الشاليه
            $chalet = Chalet::find($cleaningRecord->chalet_id);

            $response = [
                'cleaning_record' => [
                    'id' => $cleaningRecord->id,
                    'chalet_id' => $cleaningRecord->chalet_id,
                    'cleaner_id' => $cleaningRecord->cleaner_id,
                    'date' => $cleaningRecord->date,
                    'cleaning_cost' => $cleaningRecord->price ?? 0,
                    'status' => $cleaningRecord->status,
                    'created_at' => $cleaningRecord->created_at,
                    'updated_at' => $cleaningRecord->updated_at,
                ],
                'chalet' => $chalet ? [
                    'id' => $chalet->id,
                    'name' => $chalet->name,
                    'code' => $chalet->code,
                    'pass_code' => $chalet->pass_code,
                    'floor' => $chalet->floor,
                    'building' => $chalet->building,
                    'location' => $chalet->location,
                ] : null,
                'media' => [
                    'images' => $images,
                    'videos' => $videos,
                    'images_count' => $images->count(),
                    'videos_count' => $videos->count(),
                ],
                'inventory_used' => [
                    'items' => $inventoryItems,
                    'total_cost' => $inventoryItems->sum('total_cost'),
                    'items_count' => $inventoryItems->count(),
                ],
                'cleaning_info' => [
                    'type' => $cleaningType === 'regular' ? 'نظافة عادية' : 'نظافة عميقة',
                ]
            ];

            return $this->apiResponse($response, 'تم جلب تفاصيل النظافة بنجاح');

        } catch (\Exception $e) {
            Log::error('Error in getCleaningDetails: ' . $e->getMessage());
            return $this->apiResponse(null, 'حدث خطأ أثناء جلب تفاصيل النظافة', 500);
        }
    }

}
