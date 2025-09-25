<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\DeepCleaning;
use App\Models\DeepCleaningImage;
use App\Models\DeepCleaningVideo;
use App\Http\Controllers\API\ResponseTrait;
use App\Services\FirebaseNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class DeepCleaningController extends Controller
{
    use ResponseTrait;
    /**
     * عرض قائمة عمليات التنظيف العميق
     */
    public function index(Request $request)
    {
        $deepCleanings = DeepCleaning::with(['chalet', 'cleaner', 'images', 'videos'])
            ->when($request->cleaner_id, function ($query, $cleanerId) {
                $query->where('cleaner_id', $cleanerId);
            })
            ->when($request->chalet_id, function ($query, $chaletId) {
                $query->where('chalet_id', $chaletId);
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->date, function ($query, $date) {
                $query->whereDate('cleaning_date', $date);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($request->per_page ?? 15);

        return $this->apiResponse($deepCleanings, 'تم جلب عمليات التنظيف العميق بنجاح');
    }

    /**
     * إنشاء عملية تنظيف عميق جديدة
     */
    public function store(Request $request)
    {
        $request->validate([
            'chalet_id' => 'required|exists:chalets,id',
            'cleaner_id' => 'required|exists:cleaners,id',
            'cleaning_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'cleaning_type' => 'required|string|in:deep_cleaning,spring_cleaning,post_construction',
            'notes' => 'nullable|string',
            'status' => 'sometimes|string|in:pending,in_progress,completed,cancelled',
        ]);

        $deepCleaning = DeepCleaning::create($request->all());

        return $this->apiResponse($deepCleaning->load(['chalet', 'cleaner']), 'تم إنشاء عملية التنظيف العميق بنجاح', 201);
    }

    /**
     * عرض بيانات عملية تنظيف عميق محددة
     */
    public function show(DeepCleaning $deepCleaning)
    {
        $deepCleaning->load(['chalet', 'cleaner', 'images', 'videos']);

        return $this->apiResponse($deepCleaning, 'تم جلب بيانات التنظيف العميق بنجاح');
    }

    /**
     * تحديث بيانات عملية تنظيف عميق
     */
    public function update(Request $request, DeepCleaning $deepCleaning)
    {
        $request->validate([
            'cleaning_date' => 'sometimes|date',
            'start_time' => 'sometimes|date_format:H:i',
            'end_time' => 'sometimes|date_format:H:i|after:start_time',
            'cleaning_type' => 'sometimes|string|in:deep_cleaning,spring_cleaning,post_construction',
            'notes' => 'nullable|string',
            'status' => 'sometimes|string|in:pending,in_progress,completed,cancelled',
        ]);

        $deepCleaning->update($request->all());

        return $this->apiResponse($deepCleaning->fresh(['chalet', 'cleaner']), 'تم تحديث عملية التنظيف العميق بنجاح');
    }

    /**
     * رفع صور لعملية التنظيف العميق
     */
    public function uploadImages(Request $request, DeepCleaning $deepCleaning)
    {
        $request->validate([
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $uploadedImages = [];

        foreach ($request->file('images') as $image) {
            $path = $image->store('deep-cleanings/images', 'public');

            $uploadedImage = DeepCleaningImage::create([
                'deep_cleaning_id' => $deepCleaning->id,
                'image_path' => $path,
                'description' => $request->input('description', '')
            ]);

            $uploadedImages[] = $uploadedImage;
        }

        // إرسال إشعار لجميع عمال النظافة الآخرين
        try {
            $firebaseService = new FirebaseNotificationService();
            $cleaner = $request->user();
            $chalet = $deepCleaning->chalet;
            
            $title = 'رفع صور تنظيف عميق';
            $body = "قام {$cleaner->name} برفع صور للتنظيف العميق للشاليه: {$chalet->name}";
            
            $data = [
                'type' => 'deep_cleaning_images',
                'cleaning_id' => $deepCleaning->id,
                'chalet_id' => $deepCleaning->chalet_id,
                'chalet_name' => $chalet->name,
                'cleaner_name' => $cleaner->name,
                'images_count' => count($uploadedImages),
            ];
            
            $firebaseService->sendToAllCleaners($title, $body, $data, $cleaner->id);
        } catch (\Exception $e) {
            Log::error('Error sending deep cleaning images notification: ' . $e->getMessage());
        }

        return $this->apiResponse($uploadedImages, 'تم رفع الصور بنجاح');
    }

    /**
     * رفع فيديوهات لعملية التنظيف العميق
     */
    public function uploadVideos(Request $request, DeepCleaning $deepCleaning)
    {
        $request->validate([
            'videos.*' => 'required|mimes:mp4,avi,mov,wmv|max:10240'
        ]);

        $uploadedVideos = [];

        foreach ($request->file('videos') as $video) {
            $path = $video->store('deep-cleanings/videos', 'public');

            $uploadedVideo = DeepCleaningVideo::create([
                'deep_cleaning_id' => $deepCleaning->id,
                'video_path' => $path,
                'description' => $request->input('description', '')
            ]);

            $uploadedVideos[] = $uploadedVideo;
        }

        // إرسال إشعار لجميع عمال النظافة الآخرين
        try {
            $firebaseService = new FirebaseNotificationService();
            $cleaner = $request->user();
            $chalet = $deepCleaning->chalet;
            
            $title = 'رفع فيديوهات تنظيف عميق';
            $body = "قام {$cleaner->name} برفع فيديوهات للتنظيف العميق للشاليه: {$chalet->name}";
            
            $data = [
                'type' => 'deep_cleaning_videos',
                'cleaning_id' => $deepCleaning->id,
                'chalet_id' => $deepCleaning->chalet_id,
                'chalet_name' => $chalet->name,
                'cleaner_name' => $cleaner->name,
                'videos_count' => count($uploadedVideos),
            ];
            
            $firebaseService->sendToAllCleaners($title, $body, $data, $cleaner->id);
        } catch (\Exception $e) {
            Log::error('Error sending deep cleaning videos notification: ' . $e->getMessage());
        }

        return $this->apiResponse($uploadedVideos, 'تم رفع الفيديوهات بنجاح');
    }
}
