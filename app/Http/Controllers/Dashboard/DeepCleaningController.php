<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\DeepCleaning;
use App\Models\DeepCleaningImage;
use App\Models\DeepCleaningVideo;
use App\Models\Cleaner;
use App\Models\Chalet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeepCleaningController extends Controller
{
    public function index()
    {
        $deepCleanings = DeepCleaning::with(['images', 'videos', 'cleaner', 'chalet'])->orderByDesc('created_at')->paginate(10);
        return view('dashboard.deep_cleanings.index', compact('deepCleanings'));
    }

    public function create()
    {
        $cleaners = Cleaner::all();
        $chalets = Chalet::all();
        return view('dashboard.deep_cleanings.create', compact('cleaners', 'chalets'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'cleaner_id' => 'nullable|exists:cleaners,id',
            'chalet_id' => 'nullable|exists:chalets,id',
            'date' => 'required|date',
            'price' => 'nullable|string',
            'notes' => 'nullable|string',
            'before_images.*' => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
            'after_images.*' => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
        ]);
        DB::transaction(function () use ($request, $validated) {
            $deepCleaning = DeepCleaning::create($validated);
            // صور قبل التنظيف
            if ($request->hasFile('before_images')) {
                foreach ($request->file('before_images') as $image) {
                    $path = $image->store('deep_cleanings/images', 'public');
                    DeepCleaningImage::create(['deep_cleaning_id' => $deepCleaning->id, 'type' => 'before', 'image' => $path]);
                }
            }
            // صور بعد التنظيف
            if ($request->hasFile('after_images')) {
                foreach ($request->file('after_images') as $image) {
                    $path = $image->store('deep_cleanings/images', 'public');
                    DeepCleaningImage::create(['deep_cleaning_id' => $deepCleaning->id, 'type' => 'after', 'image' => $path]);
                }
            }
            // فيديو قبل التنظيف (uppy)
            if ($request->filled('uppy_video_before')) {
                DeepCleaningVideo::create(['deep_cleaning_id' => $deepCleaning->id, 'type' => 'before', 'video' => $request->uppy_video_before]);
            }
            // فيديو بعد التنظيف (uppy)
            if ($request->filled('uppy_video_after')) {
                DeepCleaningVideo::create(['deep_cleaning_id' => $deepCleaning->id, 'type' => 'after', 'video' => $request->uppy_video_after]);
            }
            // ربط المنتجات والكميات
            $syncData = [];
            if ($request->has('inventory')) {
                foreach ($request->inventory as $row) {
                    if (!empty($row['id']) && !empty($row['qty'])) {
                        $syncData[$row['id']] = ['quantity' => $row['qty']];
                    }
                }
            }
            $deepCleaning->inventory()->sync($syncData);
        });
        return redirect()->route('dashboard.deep_cleanings.index')->with('success', __('Deep cleaning created successfully.'));
    }

    public function edit(DeepCleaning $deepCleaning)
    {
        $cleaners = Cleaner::all();
        $chalets = Chalet::all();
        $deepCleaning->load(['images', 'videos']);
        return view('dashboard.deep_cleanings.edit', compact('deepCleaning', 'cleaners', 'chalets'));
    }

    public function update(Request $request, DeepCleaning $deepCleaning)
    {
        $validated = $request->validate([
            'cleaner_id' => 'nullable|exists:cleaners,id',
            'chalet_id' => 'nullable|exists:chalets,id',
            'date' => 'required|date',
            'price' => 'nullable|string',
            'notes' => 'nullable|string',
            'before_images.*' => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
            'after_images.*' => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
        ]);
        DB::transaction(function () use ($request, $deepCleaning, $validated) {
            $deepCleaning->update($validated);
            // صور قبل التنظيف
            if ($request->hasFile('before_images')) {
                foreach ($request->file('before_images') as $image) {
                    $path = $image->store('deep_cleanings/images', 'public');
                    DeepCleaningImage::create(['deep_cleaning_id' => $deepCleaning->id, 'type' => 'before', 'image' => $path]);
                }
            }
            // صور بعد التنظيف
            if ($request->hasFile('after_images')) {
                foreach ($request->file('after_images') as $image) {
                    $path = $image->store('deep_cleanings/images', 'public');
                    DeepCleaningImage::create(['deep_cleaning_id' => $deepCleaning->id, 'type' => 'after', 'image' => $path]);
                }
            }
            // فيديو قبل التنظيف (uppy)
            if ($request->filled('uppy_video_before')) {
                DeepCleaningVideo::create(['deep_cleaning_id' => $deepCleaning->id, 'type' => 'before', 'video' => $request->uppy_video_before]);
            }
            // فيديو بعد التنظيف (uppy)
            if ($request->filled('uppy_video_after')) {
                DeepCleaningVideo::create(['deep_cleaning_id' => $deepCleaning->id, 'type' => 'after', 'video' => $request->uppy_video_after]);
            }
            // ربط المنتجات والكميات
            $syncData = [];
            if ($request->has('inventory')) {
                foreach ($request->inventory as $row) {
                    if (!empty($row['id']) && !empty($row['qty'])) {
                        $syncData[$row['id']] = ['quantity' => $row['qty']];
                    }
                }
            }
            $deepCleaning->inventory()->sync($syncData);
        });
        return redirect()->route('dashboard.deep_cleanings.index')->with('success', __('Deep cleaning updated successfully.'));
    }

    public function destroy(DeepCleaning $deepCleaning)
    {
        DB::transaction(function () use ($deepCleaning) {
            $deepCleaning->images()->delete();
            $deepCleaning->videos()->delete();
            $deepCleaning->delete();
        });
        return redirect()->route('dashboard.deep_cleanings.index')->with('success', __('Deep cleaning deleted successfully.'));
    }

    public function uploadVideo(Request $request)
    {
        if ($request->hasFile('video')) {
            $path = $request->file('video')->store('deep_cleanings/videos', 'public');
            return response()->json(['path' => $path], 200);
        }
        return response()->json(['error' => 'No video uploaded'], 400);
    }
}
