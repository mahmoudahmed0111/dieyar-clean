<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Chalet;
use App\Models\Cleaner;
use App\Models\PestControl;
use App\Models\PestControlImage;
use App\Models\PestControlVideo;
use App\Http\Controllers\API\ResponseTrait;
use App\Services\FirebaseNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class PestControlController extends Controller
{
    use ResponseTrait;

    /**
     * رفع تقرير المكافحة
     */
    public function uploadPestControl(Request $request)
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
                'cleaning_time.required' => 'وقت المكافحة مطلوب',
                'cleaning_time.in' => 'وقت المكافحة يجب أن يكون before أو after',
                'description.required_if' => 'وصف المكافحة مطلوب في حالة before',
                'description.string' => 'وصف المكافحة يجب أن يكون نص',
                'description.max' => 'وصف المكافحة يجب أن يكون أقل من 1000 حرف',
                'price.required_if' => 'سعر المكافحة مطلوب في حالة after',
                'price.numeric' => 'سعر المكافحة يجب أن يكون رقم',
                'price.min' => 'سعر المكافحة يجب أن يكون أكبر من أو يساوي صفر',
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

            // البحث عن سجل المكافحة الموجود أو إنشاء جديد
            $pestControl = PestControl::where('chalet_id', $chaletId)
                ->where('cleaner_id', $cleaner->id)
                ->whereDate('date', now()->toDateString())
                ->first();

            if (!$pestControl) {
                // إنشاء سجل مكافحة جديد
                $pestControlData = [
                    'chalet_id' => $chaletId,
                    'cleaner_id' => $cleaner->id,
                    'date' => now()->toDateString(),
                    'description' => $request->description ?? '',
                    'status' => $cleaningTime === 'after' ? 'completed' : 'in_progress',
                ];

                $pestControl = PestControl::create($pestControlData);
            } else {
                // تحديث السجل الموجود
                if ($cleaningTime === 'after') {
                    $pestControl->update([
                        'status' => 'completed',
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
                            $path = $image->store('pest-control/images', 'public');
                            Log::info('Image uploaded successfully: ' . $path);

                            $uploadedImage = PestControlImage::create([
                                'pest_control_id' => $pestControl->id,
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
                            $path = $video->store('pest-control/videos', 'public');
                            Log::info('Video uploaded successfully: ' . $path);

                            $uploadedVideo = PestControlVideo::create([
                                'pest_control_id' => $pestControl->id,
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
                'pest_control_record' => [
                    'id' => $pestControl->id,
                    'chalet_id' => $pestControl->chalet_id,
                    'cleaner_id' => $pestControl->cleaner_id,
                    'date' => $pestControl->date,
                    'description' => $pestControl->description,
                    'status' => $pestControl->status,
                    'created_at' => $pestControl->created_at,
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
                'pest_control_info' => [
                    'time' => $cleaningTime === 'before' ? 'قبل المكافحة' : 'بعد المكافحة',
                ]
            ];

            // إرسال إشعار لجميع عمال النظافة الآخرين
            try {
                $firebaseService = new FirebaseNotificationService();
                $cleaner = $request->user();
                
                $title = 'تقرير مكافحة جديد';
                $body = "قام {$cleaner->name} برفع " . ($cleaningTime === 'before' ? 'الصور والفيديوهات قبل' : 'الصور والفيديوهات بعد') . " المكافحة للشاليه: {$chalet->name}";
                
                $data = [
                    'type' => 'pest_control',
                    'pest_control_id' => $pestControl->id,
                    'chalet_id' => $chaletId,
                    'chalet_name' => $chalet->name,
                    'cleaner_name' => $cleaner->name,
                    'cleaning_time' => $cleaningTime,
                    'description' => $pestControl->description,
                ];
                
                $firebaseService->sendToAllCleaners($title, $body, $data, $cleaner->id);
            } catch (\Exception $e) {
                Log::error('Error sending pest control notification: ' . $e->getMessage());
            }

            $message = 'تم رفع ' . ($cleaningTime === 'before' ? 'الصور والفيديوهات قبل' : 'الصور والفيديوهات بعد') . ' المكافحة بنجاح';

            return $this->apiResponse($response, $message, 201);

        } catch (\Exception $e) {
            Log::error('Error in uploadPestControl: ' . $e->getMessage());
            return $this->apiResponse(null, 'حدث خطأ أثناء رفع تقرير المكافحة: ' . $e->getMessage(), 500);
        }
    }

    /**
     * جلب سجل المكافحة
     */
    public function getPestControlHistory(Request $request)
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

            $query = PestControl::with(['chalet:id,name,code,pass_code'])
                ->where('cleaner_id', $cleaner->id);

            // تطبيق الفلاتر
            if ($request->chalet_id) {
                $query->where('chalet_id', $request->chalet_id);
            }

            if ($request->status) {
                $query->where('status', $request->status);
            }

            if ($request->date_from) {
                $query->whereDate('date', '>=', $request->date_from);
            }

            if ($request->date_to) {
                $query->whereDate('date', '<=', $request->date_to);
            }

            $perPage = $request->per_page ?? 15;
            $history = $query->orderBy('date', 'desc')
                ->paginate($perPage);

            // إضافة عدد الصور والفيديوهات لكل مكافحة
            $history->getCollection()->transform(function ($pestControl) {
                $pestControl->images_count = $pestControl->images()->count();
                $pestControl->videos_count = $pestControl->videos()->count();
                return $pestControl;
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
            ], 'تم جلب سجل المكافحة بنجاح');

        } catch (\Exception $e) {
            Log::error('Error in getPestControlHistory: ' . $e->getMessage());
            return $this->apiResponse(null, 'حدث خطأ أثناء جلب سجل المكافحة', 500);
        }
    }

    /**
     * جلب تفاصيل مكافحة محددة
     */
    public function getPestControlDetails(Request $request, $id)
    {
        try {
            $cleaner = $request->user();

            // جلب المكافحة مع العلاقات
            $pestControl = PestControl::with(['chalet:id,name,code,pass_code,floor,building,location'])
                ->where('id', $id)
                ->where('cleaner_id', $cleaner->id)
                ->first();

            if (!$pestControl) {
                return $this->apiResponse(null, 'تقرير المكافحة غير موجود', 404);
            }

            // جلب الصور
            $images = $pestControl->images()->get()->map(function ($image) {
                return [
                    'id' => $image->id,
                    'image' => asset('storage/' . $image->image),
                    'created_at' => $image->created_at,
                ];
            });

            // جلب الفيديوهات
            $videos = $pestControl->videos()->get()->map(function ($video) {
                return [
                    'id' => $video->id,
                    'video' => asset('storage/' . $video->video),
                    'created_at' => $video->created_at,
                ];
            });

            $response = [
                'pest_control_record' => [
                    'id' => $pestControl->id,
                    'chalet_id' => $pestControl->chalet_id,
                    'cleaner_id' => $pestControl->cleaner_id,
                    'date' => $pestControl->date,
                    'description' => $pestControl->description,
                    'status' => $pestControl->status,
                    'created_at' => $pestControl->created_at,
                    'updated_at' => $pestControl->updated_at,
                ],
                'chalet' => $pestControl->chalet,
                'media' => [
                    'images' => $images,
                    'videos' => $videos,
                    'images_count' => $images->count(),
                    'videos_count' => $videos->count(),
                ],
            ];

            return $this->apiResponse($response, 'تم جلب تفاصيل المكافحة بنجاح');

        } catch (\Exception $e) {
            Log::error('Error in getPestControlDetails: ' . $e->getMessage());
            return $this->apiResponse(null, 'حدث خطأ أثناء جلب تفاصيل المكافحة', 500);
        }
    }
}
