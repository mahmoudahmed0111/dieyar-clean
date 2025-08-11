<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Maintenance;
use App\Models\MaintenanceImage;
use App\Models\MaintenanceVideo;
use App\Models\Cleaner;
use App\Models\Chalet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MaintenanceController extends Controller
{
    public function index()
    {
        $maintenances = Maintenance::with(['images', 'videos', 'cleaner', 'chalet'])->orderByDesc('created_at')->paginate(10);
        return view('dashboard.maintenance.index', compact('maintenances'));
    }

    public function create()
    {
        $cleaners = Cleaner::all();
        $chalets = Chalet::all();
        return view('dashboard.maintenance.create', compact('cleaners', 'chalets'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'cleaner_id' => 'nullable|exists:cleaners,id',
            'chalet_id' => 'nullable|exists:chalets,id',
            'description' => 'required|string',
            'status' => 'required|in:pending,in_progress,done',
            'requested_at' => 'nullable|date',
            'completed_at' => 'nullable|date',
            'before_images.*' => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
            'after_images.*' => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
        ]);
        DB::transaction(function () use ($request, $validated) {
            $maintenance = Maintenance::create($validated);
            // صور قبل الصيانة
            if ($request->hasFile('before_images')) {
                foreach ($request->file('before_images') as $image) {
                    $path = $image->store('maintenance/images', 'public');
                    MaintenanceImage::create(['maintenance_id' => $maintenance->id, 'type' => 'before', 'image' => $path]);
                }
            }
            // صور بعد الصيانة
            if ($request->hasFile('after_images')) {
                foreach ($request->file('after_images') as $image) {
                    $path = $image->store('maintenance/images', 'public');
                    MaintenanceImage::create(['maintenance_id' => $maintenance->id, 'type' => 'after', 'image' => $path]);
                }
            }
            // فيديو قبل الصيانة (uppy)
            if ($request->filled('uppy_video_before')) {
                MaintenanceVideo::create(['maintenance_id' => $maintenance->id, 'type' => 'before', 'video' => $request->uppy_video_before]);
            }
            // فيديو بعد الصيانة (uppy)
            if ($request->filled('uppy_video_after')) {
                MaintenanceVideo::create(['maintenance_id' => $maintenance->id, 'type' => 'after', 'video' => $request->uppy_video_after]);
            }
        });
        return redirect()->route('dashboard.maintenance.index')->with('success', __('Maintenance created successfully.'));
    }

    public function edit(Maintenance $maintenance)
    {
        $cleaners = Cleaner::all();
        $chalets = Chalet::all();
        $maintenance->load(['images', 'videos']);
        return view('dashboard.maintenance.edit', compact('maintenance', 'cleaners', 'chalets'));
    }

    public function update(Request $request, Maintenance $maintenance)
    {
        $validated = $request->validate([
            'cleaner_id' => 'nullable|exists:cleaners,id',
            'chalet_id' => 'nullable|exists:chalets,id',
            'description' => 'required|string',
            'status' => 'required|in:pending,in_progress,done',
            'requested_at' => 'nullable|date',
            'completed_at' => 'nullable|date',
            'before_images.*' => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
            'after_images.*' => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
        ]);
        DB::transaction(function () use ($request, $maintenance, $validated) {
            $maintenance->update($validated);
            // صور قبل الصيانة
            if ($request->hasFile('before_images')) {
                foreach ($request->file('before_images') as $image) {
                    $path = $image->store('maintenance/images', 'public');
                    MaintenanceImage::create(['maintenance_id' => $maintenance->id, 'type' => 'before', 'image' => $path]);
                }
            }
            // صور بعد الصيانة
            if ($request->hasFile('after_images')) {
                foreach ($request->file('after_images') as $image) {
                    $path = $image->store('maintenance/images', 'public');
                    MaintenanceImage::create(['maintenance_id' => $maintenance->id, 'type' => 'after', 'image' => $path]);
                }
            }
            // فيديو قبل الصيانة (uppy)
            if ($request->filled('uppy_video_before')) {
                MaintenanceVideo::create(['maintenance_id' => $maintenance->id, 'type' => 'before', 'video' => $request->uppy_video_before]);
            }
            // فيديو بعد الصيانة (uppy)
            if ($request->filled('uppy_video_after')) {
                MaintenanceVideo::create(['maintenance_id' => $maintenance->id, 'type' => 'after', 'video' => $request->uppy_video_after]);
            }
        });
        return redirect()->route('dashboard.maintenance.index')->with('success', __('Maintenance updated successfully.'));
    }

    public function destroy(Maintenance $maintenance)
    {
        DB::transaction(function () use ($maintenance) {
            $maintenance->images()->delete();
            $maintenance->videos()->delete();
            $maintenance->delete();
        });
        return redirect()->route('dashboard.maintenance.index')->with('success', __('Maintenance deleted successfully.'));
    }

    public function uploadVideo(Request $request)
    {
        if ($request->hasFile('video')) {
            $path = $request->file('video')->store('maintenance/videos', 'public');
            return response()->json(['path' => $path], 200);
        }
        return response()->json(['error' => 'No video uploaded'], 400);
    }
}
