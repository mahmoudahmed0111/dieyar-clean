<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\PestControl;
use App\Models\PestControlImage;
use App\Models\PestControlVideo;
use App\Http\Controllers\API\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PestControlController extends Controller
{
    use ResponseTrait;
    /**
     * عرض قائمة عمليات مكافحة الآفات
     */
    public function index(Request $request)
    {
        $pestControls = PestControl::with(['chalet', 'cleaner', 'images', 'videos'])
            ->when($request->cleaner_id, function ($query, $cleanerId) {
                $query->where('cleaner_id', $cleanerId);
            })
            ->when($request->chalet_id, function ($query, $chaletId) {
                $query->where('chalet_id', $chaletId);
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->pest_type, function ($query, $type) {
                $query->where('pest_type', $type);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($request->per_page ?? 15);

        return $this->apiResponse($pestControls, 'تم جلب عمليات مكافحة الآفات بنجاح');
    }

    /**
     * إنشاء عملية مكافحة آفات جديدة
     */
    public function store(Request $request)
    {
        $request->validate([
            'chalet_id' => 'required|exists:chalets,id',
            'cleaner_id' => 'required|exists:cleaners,id',
            'treatment_date' => 'required|date',
            'pest_type' => 'required|string|in:cockroaches,ants,termites,rodents,other',
            'treatment_method' => 'required|string',
            'description' => 'required|string',
            'cost' => 'nullable|numeric|min:0',
            'status' => 'sometimes|string|in:pending,in_progress,completed,cancelled',
        ]);

        $pestControl = PestControl::create($request->all());

        return $this->apiResponse($pestControl->load(['chalet', 'cleaner']), 'تم إنشاء عملية مكافحة الآفات بنجاح', 201);
    }

    /**
     * عرض بيانات عملية مكافحة آفات محددة
     */
    public function show(PestControl $pestControl)
    {
        $pestControl->load(['chalet', 'cleaner', 'images', 'videos']);

        return $this->apiResponse($pestControl, 'تم جلب بيانات مكافحة الآفات بنجاح');
    }

    /**
     * تحديث بيانات عملية مكافحة آفات
     */
    public function update(Request $request, PestControl $pestControl)
    {
        $request->validate([
            'treatment_date' => 'sometimes|date',
            'pest_type' => 'sometimes|string|in:cockroaches,ants,termites,rodents,other',
            'treatment_method' => 'sometimes|string',
            'description' => 'sometimes|string',
            'cost' => 'nullable|numeric|min:0',
            'status' => 'sometimes|string|in:pending,in_progress,completed,cancelled',
        ]);

        $pestControl->update($request->all());

        return $this->apiResponse($pestControl->fresh(['chalet', 'cleaner']), 'تم تحديث عملية مكافحة الآفات بنجاح');
    }

    /**
     * رفع صور لعملية مكافحة الآفات
     */
    public function uploadImages(Request $request, PestControl $pestControl)
    {
        $request->validate([
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $uploadedImages = [];

        foreach ($request->file('images') as $image) {
            $path = $image->store('pest-controls/images', 'public');

            $uploadedImage = PestControlImage::create([
                'pest_control_id' => $pestControl->id,
                'image_path' => $path,
                'description' => $request->input('description', '')
            ]);

            $uploadedImages[] = $uploadedImage;
        }

        return $this->apiResponse($uploadedImages, 'تم رفع الصور بنجاح');
    }

    /**
     * رفع فيديوهات لعملية مكافحة الآفات
     */
    public function uploadVideos(Request $request, PestControl $pestControl)
    {
        $request->validate([
            'videos.*' => 'required|mimes:mp4,avi,mov,wmv|max:10240'
        ]);

        $uploadedVideos = [];

        foreach ($request->file('videos') as $video) {
            $path = $video->store('pest-controls/videos', 'public');

            $uploadedVideo = PestControlVideo::create([
                'pest_control_id' => $pestControl->id,
                'video_path' => $path,
                'description' => $request->input('description', '')
            ]);

            $uploadedVideos[] = $uploadedVideo;
        }

        return $this->apiResponse($uploadedVideos, 'تم رفع الفيديوهات بنجاح');
    }
}
