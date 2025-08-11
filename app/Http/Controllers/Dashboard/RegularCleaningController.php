<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\RegularCleaning;
use App\Models\RegularCleaningImage;
use App\Models\RegularCleaningVideo;
use App\Models\Cleaner;
use App\Models\Chalet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RegularCleaningController extends Controller
{
    public function index()
    {
        $regularCleanings = RegularCleaning::with(['images', 'videos', 'cleaner', 'chalet'])->orderByDesc('created_at')->paginate(10);
        return view('dashboard.regular_cleanings.index', compact('regularCleanings'));
    }

    public function create()
    {
        $cleaners = Cleaner::all();
        $chalets = Chalet::all();
        return view('dashboard.regular_cleanings.create', compact('cleaners', 'chalets'));
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
            $regularCleaning = RegularCleaning::create($validated);
            // صور قبل التنظيف
            if ($request->hasFile('before_images')) {
                foreach ($request->file('before_images') as $image) {
                    $path = $image->store('regular_cleanings/images', 'public');
                    RegularCleaningImage::create(['regular_cleaning_id' => $regularCleaning->id, 'type' => 'before', 'image' => $path]);
                }
            }
            // صور بعد التنظيف
            if ($request->hasFile('after_images')) {
                foreach ($request->file('after_images') as $image) {
                    $path = $image->store('regular_cleanings/images', 'public');
                    RegularCleaningImage::create(['regular_cleaning_id' => $regularCleaning->id, 'type' => 'after', 'image' => $path]);
                }
            }
            // فيديو قبل التنظيف (uppy)
            if ($request->filled('uppy_video_before')) {
                RegularCleaningVideo::create(['regular_cleaning_id' => $regularCleaning->id, 'type' => 'before', 'video' => $request->uppy_video_before]);
            }
            // فيديو بعد التنظيف (uppy)
            if ($request->filled('uppy_video_after')) {
                RegularCleaningVideo::create(['regular_cleaning_id' => $regularCleaning->id, 'type' => 'after', 'video' => $request->uppy_video_after]);
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
            $regularCleaning->inventory()->sync($syncData);
        });
        return redirect()->route('dashboard.regular_cleanings.index')->with('success', __('Regular cleaning created successfully.'));
    }

    public function edit(RegularCleaning $regularCleaning)
    {
        $cleaners = Cleaner::all();
        $chalets = Chalet::all();
        $regularCleaning->load(['images', 'videos']);
        return view('dashboard.regular_cleanings.edit', compact('regularCleaning', 'cleaners', 'chalets'));
    }

    public function update(Request $request, RegularCleaning $regularCleaning)
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
        DB::transaction(function () use ($request, $regularCleaning, $validated) {
            $regularCleaning->update($validated);
            // صور قبل التنظيف
            if ($request->hasFile('before_images')) {
                foreach ($request->file('before_images') as $image) {
                    $path = $image->store('regular_cleanings/images', 'public');
                    RegularCleaningImage::create(['regular_cleaning_id' => $regularCleaning->id, 'type' => 'before', 'image' => $path]);
                }
            }
            // صور بعد التنظيف
            if ($request->hasFile('after_images')) {
                foreach ($request->file('after_images') as $image) {
                    $path = $image->store('regular_cleanings/images', 'public');
                    RegularCleaningImage::create(['regular_cleaning_id' => $regularCleaning->id, 'type' => 'after', 'image' => $path]);
                }
            }
            // فيديو قبل التنظيف (uppy)
            if ($request->filled('uppy_video_before')) {
                RegularCleaningVideo::create(['regular_cleaning_id' => $regularCleaning->id, 'type' => 'before', 'video' => $request->uppy_video_before]);
            }
            // فيديو بعد التنظيف (uppy)
            if ($request->filled('uppy_video_after')) {
                RegularCleaningVideo::create(['regular_cleaning_id' => $regularCleaning->id, 'type' => 'after', 'video' => $request->uppy_video_after]);
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
            $regularCleaning->inventory()->sync($syncData);
        });
        return redirect()->route('dashboard.regular_cleanings.index')->with('success', __('Regular cleaning updated successfully.'));
    }

    public function destroy(RegularCleaning $regularCleaning)
    {
        DB::transaction(function () use ($regularCleaning) {
            $regularCleaning->images()->delete();
            $regularCleaning->videos()->delete();
            $regularCleaning->delete();
        });
        return redirect()->route('dashboard.regular_cleanings.index')->with('success', __('Regular cleaning deleted successfully.'));
    }

    public function uploadVideo(Request $request)
    {
        if ($request->hasFile('video')) {
            $path = $request->file('video')->store('regular_cleanings/videos', 'public');
            return response()->json(['path' => $path], 200);
        }
        return response()->json(['error' => 'No video uploaded'], 400);
    }
}
