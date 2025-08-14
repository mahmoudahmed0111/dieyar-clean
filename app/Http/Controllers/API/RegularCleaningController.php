<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\RegularCleaning;
use App\Models\RegularCleaningImage;
use App\Models\RegularCleaningVideo;
use App\Http\Controllers\API\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RegularCleaningController extends Controller
{
    use ResponseTrait;
    /**
     * عرض قائمة عمليات التنظيف العادي
     */
    public function index(Request $request)
    {
        $regularCleanings = RegularCleaning::with(['chalet', 'cleaner', 'images', 'videos'])
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

        return $this->apiResponse($regularCleanings, 'تم جلب عمليات التنظيف العادي بنجاح');
    }

    /**
     * إنشاء عملية تنظيف عادي جديدة
     */
    public function store(Request $request)
    {
        $request->validate([
            'chalet_id' => 'required|exists:chalets,id',
            'cleaner_id' => 'required|exists:cleaners,id',
            'cleaning_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'notes' => 'nullable|string',
            'status' => 'sometimes|string|in:pending,in_progress,completed,cancelled',
        ]);

        $regularCleaning = RegularCleaning::create($request->all());

        return $this->apiResponse($regularCleaning->load(['chalet', 'cleaner']), 'تم إنشاء عملية التنظيف العادي بنجاح', 201);
    }

    /**
     * عرض بيانات عملية تنظيف عادي محددة
     */
    public function show(RegularCleaning $regularCleaning)
    {
        $regularCleaning->load(['chalet', 'cleaner', 'images', 'videos']);

        return $this->apiResponse($regularCleaning, 'تم جلب بيانات التنظيف العادي بنجاح');
    }

    /**
     * تحديث بيانات عملية تنظيف عادي
     */
    public function update(Request $request, RegularCleaning $regularCleaning)
    {
        $request->validate([
            'cleaning_date' => 'sometimes|date',
            'start_time' => 'sometimes|date_format:H:i',
            'end_time' => 'sometimes|date_format:H:i|after:start_time',
            'notes' => 'nullable|string',
            'status' => 'sometimes|string|in:pending,in_progress,completed,cancelled',
        ]);

        $regularCleaning->update($request->all());

        return $this->apiResponse($regularCleaning->fresh(['chalet', 'cleaner']), 'تم تحديث عملية التنظيف العادي بنجاح');
    }

    /**
     * رفع صور لعملية التنظيف العادي
     */
    public function uploadImages(Request $request, RegularCleaning $regularCleaning)
    {
        $request->validate([
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $uploadedImages = [];

        foreach ($request->file('images') as $image) {
            $path = $image->store('regular-cleanings/images', 'public');

            $uploadedImage = RegularCleaningImage::create([
                'regular_cleaning_id' => $regularCleaning->id,
                'image_path' => $path,
                'description' => $request->input('description', '')
            ]);

            $uploadedImages[] = $uploadedImage;
        }

        return $this->apiResponse($uploadedImages, 'تم رفع الصور بنجاح');
    }

    /**
     * رفع فيديوهات لعملية التنظيف العادي
     */
    public function uploadVideos(Request $request, RegularCleaning $regularCleaning)
    {
        $request->validate([
            'videos.*' => 'required|mimes:mp4,avi,mov,wmv|max:10240'
        ]);

        $uploadedVideos = [];

        foreach ($request->file('videos') as $video) {
            $path = $video->store('regular-cleanings/videos', 'public');

            $uploadedVideo = RegularCleaningVideo::create([
                'regular_cleaning_id' => $regularCleaning->id,
                'video_path' => $path,
                'description' => $request->input('description', '')
            ]);

            $uploadedVideos[] = $uploadedVideo;
        }

        return $this->apiResponse($uploadedVideos, 'تم رفع الفيديوهات بنجاح');
    }
}
