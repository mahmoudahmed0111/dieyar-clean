<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Damage;
use App\Models\DamageImage;
use App\Models\DamageVideo;
use App\Http\Controllers\API\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DamageController extends Controller
{
    use ResponseTrait;
    /**
     * عرض قائمة الأضرار
     */
    public function index(Request $request)
    {
        $damages = Damage::with(['chalet', 'cleaner', 'images', 'videos'])
            ->when($request->cleaner_id, function ($query, $cleanerId) {
                $query->where('cleaner_id', $cleanerId);
            })
            ->when($request->chalet_id, function ($query, $chaletId) {
                $query->where('chalet_id', $chaletId);
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->damage_type, function ($query, $type) {
                $query->where('damage_type', $type);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($request->per_page ?? 15);
        return $this->apiResponse($damages, 'تم جلب الأضرار بنجاح', 200);
    }

    /**
     * إنشاء ضرر جديد
     */
    public function store(Request $request)
    {
        $request->validate([
            'chalet_id' => 'required|exists:chalets,id',
            'cleaner_id' => 'required|exists:cleaners,id',
            'damage_date' => 'required|date',
            'damage_type' => 'required|string|in:structural,electrical,plumbing,furniture,other',
            'description' => 'required|string',
            'severity' => 'required|string|in:low,medium,high,critical',
            'estimated_cost' => 'nullable|numeric|min:0',
            'status' => 'sometimes|string|in:reported,assessed,repairing,repaired',
        ]);

        $damage = Damage::create($request->all());

        return $this->apiResponse($damage->load(['chalet', 'cleaner']), 'تم إنشاء تقرير الضرر بنجاح', 201);
    }

    /**
     * عرض بيانات ضرر محدد
     */
    public function show(Damage $damage)
    {
        $damage->load(['chalet', 'cleaner', 'images', 'videos']);

        return $this->apiResponse($damage, 'تم جلب بيانات الضرر بنجاح');
    }

    /**
     * تحديث بيانات ضرر
     */
    public function update(Request $request, Damage $damage)
    {
        $request->validate([
            'damage_date' => 'sometimes|date',
            'damage_type' => 'sometimes|string|in:structural,electrical,plumbing,furniture,other',
            'description' => 'sometimes|string',
            'severity' => 'sometimes|string|in:low,medium,high,critical',
            'estimated_cost' => 'nullable|numeric|min:0',
            'status' => 'sometimes|string|in:reported,assessed,repairing,repaired',
        ]);

        $damage->update($request->all());

        return $this->apiResponse($damage->fresh(['chalet', 'cleaner']), 'تم تحديث تقرير الضرر بنجاح');
    }

    /**
     * رفع صور للضرر
     */
    public function uploadImages(Request $request, Damage $damage)
    {
        $request->validate([
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $uploadedImages = [];

        foreach ($request->file('images') as $image) {
            $path = $image->store('damages/images', 'public');

            $uploadedImage = DamageImage::create([
                'damage_id' => $damage->id,
                'image_path' => $path,
                'description' => $request->input('description', '')
            ]);

            $uploadedImages[] = $uploadedImage;
        }

        return $this->apiResponse($uploadedImages, 'تم رفع الصور بنجاح');
    }

    /**
     * رفع فيديوهات للضرر
     */
    public function uploadVideos(Request $request, Damage $damage)
    {
        $request->validate([
            'videos.*' => 'required|mimes:mp4,avi,mov,wmv|max:10240'
        ]);

        $uploadedVideos = [];

        foreach ($request->file('videos') as $video) {
            $path = $video->store('damages/videos', 'public');

            $uploadedVideo = DamageVideo::create([
                'damage_id' => $damage->id,
                'video_path' => $path,
                'description' => $request->input('description', '')
            ]);

            $uploadedVideos[] = $uploadedVideo;
        }

        return $this->apiResponse($uploadedVideos, 'تم رفع الفيديوهات بنجاح');
    }
}
