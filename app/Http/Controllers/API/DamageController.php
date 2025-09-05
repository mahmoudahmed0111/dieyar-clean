<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Chalet;
use App\Models\Cleaner;
use App\Models\Damage;
use App\Models\DamageImage;
use App\Models\DamageVideo;
use App\Http\Controllers\API\ResponseTrait;
use App\Services\FirebaseNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class DamageController extends Controller
{
    use ResponseTrait;

    /**
     * رفع تقرير ضرر جديد
     */
    public function uploadDamage(Request $request)
    {
        try {
            // التحقق من البيانات الأساسية
            $validator = Validator::make($request->all(), [
                'chalet_id' => 'required|exists:chalets,id',
                'description' => 'required|string|max:1000',
                'price' => 'required|numeric|min:0',
                'images' => 'nullable|array',
                'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240', // 10MB max
                'videos' => 'nullable|array',
                'videos.*' => 'nullable|mimes:mp4,avi,mov,wmv,webm|max:102400', // 100MB max
            ], [
                'chalet_id.required' => 'معرف الشاليه مطلوب',
                'chalet_id.exists' => 'الشاليه غير موجود',
                'description.required' => 'وصف الضرر مطلوب',
                'description.string' => 'وصف الضرر يجب أن يكون نص',
                'description.max' => 'وصف الضرر يجب أن يكون أقل من 1000 حرف',
                'price.required' => 'سعر الضرر مطلوب',
                'price.numeric' => 'سعر الضرر يجب أن يكون رقم',
                'price.min' => 'سعر الضرر يجب أن يكون أكبر من أو يساوي صفر',
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

            // إنشاء سجل الضرر
            $damageData = [
                'chalet_id' => $chaletId,
                'cleaner_id' => $cleaner->id,
                'description' => $request->description,
                'price' => $request->price,
                'reported_at' => now(),
                'status' => 'pending',
            ];

            $damage = Damage::create($damageData);

            // رفع الصور
            $uploadedImages = [];
            if ($request->hasFile('images')) {
                Log::info('Found images in request: ' . count($request->file('images')));
                foreach ($request->file('images') as $index => $image) {
                    try {
                        if ($image && $image->isValid()) {
                            $path = $image->store('damages/images', 'public');
                            Log::info('Image uploaded successfully: ' . $path);

                            $uploadedImage = DamageImage::create([
                                'damage_id' => $damage->id,
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
                            $path = $video->store('damages/videos', 'public');
                            Log::info('Video uploaded successfully: ' . $path);

                            $uploadedVideo = DamageVideo::create([
                                'damage_id' => $damage->id,
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
                'damage_record' => [
                    'id' => $damage->id,
                    'chalet_id' => $damage->chalet_id,
                    'cleaner_id' => $damage->cleaner_id,
                    'description' => $damage->description,
                    'price' => $damage->price,
                    'reported_at' => $damage->reported_at,
                    'status' => $damage->status,
                    'created_at' => $damage->created_at,
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
            ];

            // إرسال إشعار لجميع عمال النظافة الآخرين
            try {
                $firebaseService = new FirebaseNotificationService();
                $cleaner = $request->user();
                
                $title = 'بلاغ ضرر جديد';
                $body = "قام {$cleaner->name} بتسجيل بلاغ ضرر للشاليه: {$chalet->name}";
                
                $data = [
                    'type' => 'damage',
                    'damage_id' => $damage->id,
                    'chalet_id' => $chaletId,
                    'chalet_name' => $chalet->name,
                    'cleaner_name' => $cleaner->name,
                    'description' => $damage->description,
                    'price' => $damage->price,
                ];
                
                $firebaseService->sendToAllCleaners($title, $body, $data, $cleaner->id);
            } catch (\Exception $e) {
                Log::error('Error sending damage notification: ' . $e->getMessage());
            }

            return $this->apiResponse(null, 'تم رفع تقرير الضرر بنجاح', 201);

        } catch (\Exception $e) {
            Log::error('Error in reportDamage: ' . $e->getMessage());
            return $this->apiResponse(null, 'حدث خطأ أثناء رفع تقرير الضرر: ' . $e->getMessage(), 500);
        }
    }

}
