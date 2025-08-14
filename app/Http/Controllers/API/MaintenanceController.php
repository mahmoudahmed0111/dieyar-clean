<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Maintenance;
use App\Models\MaintenanceImage;
use App\Models\MaintenanceVideo;
use App\Http\Controllers\API\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MaintenanceController extends Controller
{
    use ResponseTrait;
    /**
     * عرض قائمة عمليات الصيانة
     */
    public function index(Request $request)
    {
        $maintenance = Maintenance::with(['chalet', 'cleaner', 'images', 'videos'])
            ->when($request->cleaner_id, function ($query, $cleanerId) {
                $query->where('cleaner_id', $cleanerId);
            })
            ->when($request->chalet_id, function ($query, $chaletId) {
                $query->where('chalet_id', $chaletId);
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->maintenance_type, function ($query, $type) {
                $query->where('maintenance_type', $type);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($request->per_page ?? 15);

        return $this->apiResponse($maintenance, 'تم جلب عمليات الصيانة بنجاح');
    }

    /**
     * إنشاء عملية صيانة جديدة
     */
    public function store(Request $request)
    {
        $request->validate([
            'chalet_id' => 'required|exists:chalets,id',
            'cleaner_id' => 'required|exists:cleaners,id',
            'maintenance_date' => 'required|date',
            'maintenance_type' => 'required|string|in:electrical,plumbing,hvac,general',
            'description' => 'required|string',
            'cost' => 'nullable|numeric|min:0',
            'status' => 'sometimes|string|in:pending,in_progress,completed,cancelled',
        ]);

        $maintenance = Maintenance::create($request->all());

        return $this->apiResponse($maintenance->load(['chalet', 'cleaner']), 'تم إنشاء عملية الصيانة بنجاح', 201);
    }

    /**
     * عرض بيانات عملية صيانة محددة
     */
    public function show(Maintenance $maintenance)
    {
        $maintenance->load(['chalet', 'cleaner', 'images', 'videos']);

        return $this->apiResponse($maintenance, 'تم جلب بيانات الصيانة بنجاح');
    }

    /**
     * تحديث بيانات عملية صيانة
     */
    public function update(Request $request, Maintenance $maintenance)
    {
        $request->validate([
            'maintenance_date' => 'sometimes|date',
            'maintenance_type' => 'sometimes|string|in:electrical,plumbing,hvac,general',
            'description' => 'sometimes|string',
            'cost' => 'nullable|numeric|min:0',
            'status' => 'sometimes|string|in:pending,in_progress,completed,cancelled',
        ]);

        $maintenance->update($request->all());

        return $this->apiResponse($maintenance->fresh(['chalet', 'cleaner']), 'تم تحديث عملية الصيانة بنجاح');
    }

    /**
     * رفع صور لعملية الصيانة
     */
    public function uploadImages(Request $request, Maintenance $maintenance)
    {
        $request->validate([
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $uploadedImages = [];

        foreach ($request->file('images') as $image) {
            $path = $image->store('maintenance/images', 'public');

            $uploadedImage = MaintenanceImage::create([
                'maintenance_id' => $maintenance->id,
                'image_path' => $path,
                'description' => $request->input('description', '')
            ]);

            $uploadedImages[] = $uploadedImage;
        }

        return $this->apiResponse($uploadedImages, 'تم رفع الصور بنجاح');
    }

    /**
     * رفع فيديوهات لعملية الصيانة
     */
    public function uploadVideos(Request $request, Maintenance $maintenance)
    {
        $request->validate([
            'videos.*' => 'required|mimes:mp4,avi,mov,wmv|max:10240'
        ]);

        $uploadedVideos = [];

        foreach ($request->file('videos') as $video) {
            $path = $video->store('maintenance/videos', 'public');

            $uploadedVideo = MaintenanceVideo::create([
                'maintenance_id' => $maintenance->id,
                'video_path' => $path,
                'description' => $request->input('description', '')
            ]);

            $uploadedVideos[] = $uploadedVideo;
        }

        return $this->apiResponse($uploadedVideos, 'تم رفع الفيديوهات بنجاح');
    }
}
