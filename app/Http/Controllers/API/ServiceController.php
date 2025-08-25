<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Chalet;
use App\Models\Cleaner;
use App\Models\Maintenance;
use App\Models\MaintenanceImage;
use App\Models\MaintenanceVideo;
use App\Models\PestControl;
use App\Models\PestControlImage;
use App\Models\PestControlVideo;
use App\Http\Controllers\API\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ServiceController extends Controller
{
    use ResponseTrait;

    /**
     * رفع تقرير الخدمة (صيانة أو مكافحة)
     */
    public function uploadService(Request $request)
    {
        try {
            // التحقق من البيانات الأساسية
            $validator = Validator::make($request->all(), [
                'chalet_id' => 'required|exists:chalets,id',
                'service_type' => 'required|in:maintenance,pest_control',
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
                'service_type.required' => 'نوع الخدمة مطلوب',
                'service_type.in' => 'نوع الخدمة يجب أن يكون maintenance أو pest_control',
                'cleaning_time.required' => 'وقت الخدمة مطلوب',
                'cleaning_time.in' => 'وقت الخدمة يجب أن يكون before أو after',
                'description.required_if' => 'وصف الخدمة مطلوب في حالة before',
                'description.string' => 'وصف الخدمة يجب أن يكون نص',
                'description.max' => 'وصف الخدمة يجب أن يكون أقل من 1000 حرف',
                'price.required_if' => 'سعر الخدمة مطلوب في حالة after',
                'price.numeric' => 'سعر الخدمة يجب أن يكون رقم',
                'price.min' => 'سعر الخدمة يجب أن يكون أكبر من أو يساوي صفر',
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
            $serviceType = $request->service_type;
            $cleaningTime = $request->cleaning_time;

            // إنشاء أو تحديث السجل حسب نوع الخدمة
            if ($serviceType === 'maintenance') {
                $record = $this->handleMaintenance($cleaner, $chaletId, $request);
            } else {
                $record = $this->handlePestControl($cleaner, $chaletId, $request);
            }

            // رفع الصور
            $uploadedImages = $this->uploadImages($request, $record, $serviceType);

            // رفع الفيديوهات
            $uploadedVideos = $this->uploadVideos($request, $record, $serviceType);

            // معلومات الشاليه
            $chalet = Chalet::find($chaletId);

            // تجميع البيانات للرد
            $response = [
                'service_record' => [
                    'id' => $record->id,
                    'chalet_id' => $record->chalet_id,
                    'cleaner_id' => $record->cleaner_id,
                    'service_type' => $serviceType,
                    'status' => $record->status,
                    'created_at' => $record->created_at,
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
                'service_info' => [
                    'type' => $serviceType === 'maintenance' ? 'صيانة' : 'مكافحة',
                    'time' => $cleaningTime === 'before' ? 'قبل الخدمة' : 'بعد الخدمة',
                ]
            ];

            // إضافة البيانات الخاصة بكل نوع خدمة
            if ($serviceType === 'maintenance') {
                $response['service_record']['description'] = $record->description;
                $response['service_record']['requested_at'] = $record->requested_at;
                $response['service_record']['completed_at'] = $record->completed_at;
            } else {
                $response['service_record']['description'] = $record->description;
                $response['service_record']['date'] = $record->date;
            }

            $serviceName = $serviceType === 'maintenance' ? 'الصيانة' : 'المكافحة';
            $message = 'تم رفع ' . ($cleaningTime === 'before' ? 'الصور والفيديوهات قبل' : 'الصور والفيديوهات بعد') . ' ' . $serviceName . ' بنجاح';

            return $this->apiResponse(null, $message, 201);

        } catch (\Exception $e) {
            Log::error('Error in uploadService: ' . $e->getMessage());
            return $this->apiResponse(null, 'حدث خطأ أثناء رفع تقرير الخدمة: ' . $e->getMessage(), 500);
        }
    }

    /**
     * معالجة الصيانة
     */
    private function handleMaintenance($cleaner, $chaletId, $request)
    {
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
                'status' => $cleaningTime === 'after' ? 'done' : 'in_progress',
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
                    'status' => 'done',
                    'completed_at' => now(),
                ]);
            }
        }

        return $maintenance;
    }

    /**
     * معالجة المكافحة
     */
    private function handlePestControl($cleaner, $chaletId, $request)
    {
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
                'status' => $cleaningTime === 'after' ? 'done' : 'pending',
            ];

            $pestControl = PestControl::create($pestControlData);
        } else {
            // تحديث السجل الموجود
            if ($cleaningTime === 'after') {
                $pestControl->update([
                    'status' => 'done',
                ]);
            }
        }

        return $pestControl;
    }

    /**
     * رفع الصور
     */
    private function uploadImages($request, $record, $serviceType)
    {
        $uploadedImages = [];

        if ($request->hasFile('images')) {
            Log::info('Found images in request: ' . count($request->file('images')));

            foreach ($request->file('images') as $index => $image) {
                try {
                    if ($image && $image->isValid()) {
                        $folder = $serviceType === 'maintenance' ? 'maintenance/images' : 'pest-control/images';
                        $path = $image->store($folder, 'public');
                        Log::info('Image uploaded successfully: ' . $path);

                        if ($serviceType === 'maintenance') {
                            $uploadedImage = MaintenanceImage::create([
                                'maintenance_id' => $record->id,
                                'image' => $path,
                            ]);
                        } else {
                            $uploadedImage = PestControlImage::create([
                                'pest_control_id' => $record->id,
                                'image' => $path,
                            ]);
                        }

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

        return $uploadedImages;
    }

    /**
     * رفع الفيديوهات
     */
    private function uploadVideos($request, $record, $serviceType)
    {
        $uploadedVideos = [];

        if ($request->hasFile('videos')) {
            Log::info('Found videos in request: ' . count($request->file('videos')));

            foreach ($request->file('videos') as $index => $video) {
                try {
                    if ($video && $video->isValid()) {
                        $folder = $serviceType === 'maintenance' ? 'maintenance/videos' : 'pest-control/videos';
                        $path = $video->store($folder, 'public');
                        Log::info('Video uploaded successfully: ' . $path);

                        if ($serviceType === 'maintenance') {
                            $uploadedVideo = MaintenanceVideo::create([
                                'maintenance_id' => $record->id,
                                'video' => $path,
                            ]);
                        } else {
                            $uploadedVideo = PestControlVideo::create([
                                'pest_control_id' => $record->id,
                                'video' => $path,
                            ]);
                        }

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

        return $uploadedVideos;
    }

}
