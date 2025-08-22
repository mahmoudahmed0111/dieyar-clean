<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Chalet;
use App\Models\Cleaner;
use App\Models\Maintenance;
use App\Models\MaintenanceImage;
use App\Models\MaintenanceVideo;
use App\Http\Controllers\API\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class MaintenanceController extends Controller
{
    use ResponseTrait;

    /**
     * رفع تقرير الصيانة
     */
    public function uploadMaintenance(Request $request)
    {
        try {
            // التحقق من البيانات الأساسية
            $validator = Validator::make($request->all(), [
                'chalet_id' => 'required|exists:chalets,id',
                'cleaning_time' => 'required|in:before,after',
                'description' => 'required_if:cleaning_time,before|string|max:1000',
                'price' => 'required_if:cleaning_time,after|numeric|min:0',
                'images' => 'nullable|array',
                'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240', // 10MB max
                'videos' => 'nullable|array',
                'videos.*' => 'nullable|mimes:mp4,avi,mov,wmv,webm|max:102400', // 100MB max
            ], [
                'chalet_id.required' => 'معرف الشاليه مطلوب',
                'chalet_id.exists' => 'الشاليه غير موجود',
                'cleaning_time.required' => 'وقت الصيانة مطلوب',
                'cleaning_time.in' => 'وقت الصيانة يجب أن يكون before أو after',
                'description.required_if' => 'وصف الصيانة مطلوب في حالة before',
                'description.string' => 'وصف الصيانة يجب أن يكون نص',
                'description.max' => 'وصف الصيانة يجب أن يكون أقل من 1000 حرف',
                'price.required_if' => 'سعر الصيانة مطلوب في حالة after',
                'price.numeric' => 'سعر الصيانة يجب أن يكون رقم',
                'price.min' => 'سعر الصيانة يجب أن يكون أكبر من أو يساوي صفر',
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
            $chaletId = $request->chalet_id;
            $cleaningTime = $request->cleaning_time;

            // البحث عن سجل الصيانة الموجود أو إنشاء جديد
            $maintenance = Maintenance::where('chalet_id', $chaletId)
                ->where('cleaner_id', $cleaner->id)
                ->whereDate('requested_at', now()->toDateString())
                ->first();

            if (!$maintenance) {
                // إنشاء سجل صيانة جديد
                $maintenanceData = [
                    'chalet_id' => $chaletId,
                    'cleaner_id' => $cleaner->id,
                    'description' => $request->description ?? '',
                    'status' => $cleaningTime === 'after' ? 'completed' : 'in_progress',
                    'requested_at' => now(),
                ];

                if ($cleaningTime === 'after') {
                    $maintenanceData['completed_at'] = now();
                }

                $maintenance = Maintenance::create($maintenanceData);
            } else {
                // تحديث السجل الموجود
                if ($cleaningTime === 'after') {
                    $maintenance->update([
                        'status' => 'completed',
                        'completed_at' => now(),
                    ]);
                }
            }

            // رفع الصور
            $uploadedImages = [];
            if ($request->hasFile('images')) {
                Log::info('Found images in request: ' . count($request->file('images')));
                foreach ($request->file('images') as $index => $image) {
                    try {
                        if ($image && $image->isValid()) {
                            $path = $image->store('maintenance/images', 'public');
                            Log::info('Image uploaded successfully: ' . $path);

                            $uploadedImage = MaintenanceImage::create([
                                'maintenance_id' => $maintenance->id,
                                'image' => $path,
                            ]);

                            $uploadedImages[] = [
                                'id' => $uploadedImage->id,
                                'image' => asset('storage/' . $path),
                            ];
                        } else {
                            Log::warning('Invalid image file at index: ' . $index);
                        }
                    } catch (\Exception $e) {
                        Log::error('Error uploading image: ' . $e->getMessage());
                    }
                }
            } else {
                Log::info('No images found in request');
            }

            // رفع الفيديوهات
            $uploadedVideos = [];
            if ($request->hasFile('videos')) {
                Log::info('Found videos in request: ' . count($request->file('videos')));
                foreach ($request->file('videos') as $index => $video) {
                    try {
                        if ($video && $video->isValid()) {
                            $path = $video->store('maintenance/videos', 'public');
                            Log::info('Video uploaded successfully: ' . $path);

                            $uploadedVideo = MaintenanceVideo::create([
                                'maintenance_id' => $maintenance->id,
                                'video' => $path,
                            ]);

                            $uploadedVideos[] = [
                                'id' => $uploadedVideo->id,
                                'video' => asset('storage/' . $path),
                            ];
                        } else {
                            Log::warning('Invalid video file at index: ' . $index);
                        }
                    } catch (\Exception $e) {
                        Log::error('Error uploading video: ' . $e->getMessage());
                    }
                }
            } else {
                Log::info('No videos found in request');
            }

            // معلومات الشاليه
            $chalet = Chalet::find($chaletId);

            // تجميع البيانات للرد
            $response = [
                'maintenance_record' => [
                    'id' => $maintenance->id,
                    'chalet_id' => $maintenance->chalet_id,
                    'cleaner_id' => $maintenance->cleaner_id,
                    'description' => $maintenance->description,
                    'status' => $maintenance->status,
                    'requested_at' => $maintenance->requested_at,
                    'completed_at' => $maintenance->completed_at,
                    'created_at' => $maintenance->created_at,
                ],
                'chalet' => $chalet ? [
                    'id' => $chalet->id,
                    'name' => $chalet->name,
                    'code' => $chalet->code,
                    'pass_code' => $chalet->pass_code,
                ] : null,
                'uploaded_media' => [
                    'images' => $uploadedImages,
                    'videos' => $uploadedVideos,
                    'images_count' => count($uploadedImages),
                    'videos_count' => count($uploadedVideos),
                ],
                'maintenance_info' => [
                    'time' => $cleaningTime === 'before' ? 'قبل الصيانة' : 'بعد الصيانة',
                ]
            ];

            $message = 'تم رفع ' . ($cleaningTime === 'before' ? 'الصور والفيديوهات قبل' : 'الصور والفيديوهات بعد') . ' الصيانة بنجاح';

            return $this->apiResponse($response, $message, 201);

        } catch (\Exception $e) {
            Log::error('Error in uploadMaintenance: ' . $e->getMessage());
            return $this->apiResponse(null, 'حدث خطأ أثناء رفع تقرير الصيانة: ' . $e->getMessage(), 500);
        }
    }

    /**
     * جلب سجل الصيانة
     */
    public function getMaintenanceHistory(Request $request)
    {
        try {
            $cleaner = $request->user();

            $validator = Validator::make($request->all(), [
                'chalet_id' => 'nullable|exists:chalets,id',
                'status' => 'nullable|in:in_progress,completed',
                'date_from' => 'nullable|date',
                'date_to' => 'nullable|date|after_or_equal:date_from',
                'per_page' => 'nullable|integer|min:1|max:100',
            ]);

            if ($validator->fails()) {
                return $this->apiResponse(null, $validator->errors()->first(), 422);
            }

            $query = Maintenance::with(['chalet:id,name,code,pass_code'])
                ->where('cleaner_id', $cleaner->id);

            // تطبيق الفلاتر
            if ($request->chalet_id) {
                $query->where('chalet_id', $request->chalet_id);
            }

            if ($request->status) {
                $query->where('status', $request->status);
            }

            if ($request->date_from) {
                $query->whereDate('requested_at', '>=', $request->date_from);
            }

            if ($request->date_to) {
                $query->whereDate('requested_at', '<=', $request->date_to);
            }

            $perPage = $request->per_page ?? 15;
            $history = $query->orderBy('requested_at', 'desc')
                ->paginate($perPage);

            // إضافة عدد الصور والفيديوهات لكل صيانة
            $history->getCollection()->transform(function ($maintenance) {
                $maintenance->images_count = $maintenance->images()->count();
                $maintenance->videos_count = $maintenance->videos()->count();
                return $maintenance;
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
            ], 'تم جلب سجل الصيانة بنجاح');

        } catch (\Exception $e) {
            Log::error('Error in getMaintenanceHistory: ' . $e->getMessage());
            return $this->apiResponse(null, 'حدث خطأ أثناء جلب سجل الصيانة', 500);
        }
    }

    /**
     * جلب تفاصيل صيانة محددة
     */
    public function getMaintenanceDetails(Request $request, $id)
    {
        try {
            $cleaner = $request->user();

            // جلب الصيانة مع العلاقات
            $maintenance = Maintenance::with(['chalet:id,name,code,pass_code,floor,building,location'])
                ->where('id', $id)
                ->where('cleaner_id', $cleaner->id)
                ->first();

            if (!$maintenance) {
                return $this->apiResponse(null, 'تقرير الصيانة غير موجود', 404);
            }

            // جلب الصور
            $images = $maintenance->images()->get()->map(function ($image) {
                return [
                    'id' => $image->id,
                    'image' => asset('storage/' . $image->image),
                    'created_at' => $image->created_at,
                ];
            });

            // جلب الفيديوهات
            $videos = $maintenance->videos()->get()->map(function ($video) {
                return [
                    'id' => $video->id,
                    'video' => asset('storage/' . $video->video),
                    'created_at' => $video->created_at,
                ];
            });

            $response = [
                'maintenance_record' => [
                    'id' => $maintenance->id,
                    'chalet_id' => $maintenance->chalet_id,
                    'cleaner_id' => $maintenance->cleaner_id,
                    'description' => $maintenance->description,
                    'status' => $maintenance->status,
                    'requested_at' => $maintenance->requested_at,
                    'completed_at' => $maintenance->completed_at,
                    'created_at' => $maintenance->created_at,
                    'updated_at' => $maintenance->updated_at,
                ],
                'chalet' => $maintenance->chalet,
                'media' => [
                    'images' => $images,
                    'videos' => $videos,
                    'images_count' => $images->count(),
                    'videos_count' => $videos->count(),
                ],
            ];

            return $this->apiResponse($response, 'تم جلب تفاصيل الصيانة بنجاح');

        } catch (\Exception $e) {
            Log::error('Error in getMaintenanceDetails: ' . $e->getMessage());
            return $this->apiResponse(null, 'حدث خطأ أثناء جلب تفاصيل الصيانة', 500);
        }
    }
}
