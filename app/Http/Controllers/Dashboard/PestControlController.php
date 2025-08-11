<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\PestControl;
use App\Models\PestControlImage;
use App\Models\PestControlVideo;
use App\Models\Cleaner;
use App\Models\Chalet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PestControlController extends Controller
{
    public function index()
    {
        $pestControls = PestControl::with(['images', 'videos', 'cleaner', 'chalet'])->orderByDesc('created_at')->paginate(10);
        return view('dashboard.pest_controls.index', compact('pestControls'));
    }

    public function create()
    {
        $cleaners = Cleaner::all();
        $chalets = Chalet::all();
        return view('dashboard.pest_controls.create', compact('cleaners', 'chalets'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'cleaner_id' => 'nullable|exists:cleaners,id',
            'chalet_id' => 'nullable|exists:chalets,id',
            'date' => 'required|date',
            'description' => 'nullable|string',
            'status' => 'required|in:pending,done',
            'notes' => 'nullable|string',
            'before_images.*' => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
            'after_images.*' => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
        ]);
        DB::transaction(function () use ($request, $validated) {
            $pestControl = PestControl::create($validated);
            // صور قبل المبيدات
            if ($request->hasFile('before_images')) {
                foreach ($request->file('before_images') as $image) {
                    $path = $image->store('pest_controls/images', 'public');
                    PestControlImage::create(['pest_control_id' => $pestControl->id, 'type' => 'before', 'image' => $path]);
                }
            }
            // صور بعد المبيدات
            if ($request->hasFile('after_images')) {
                foreach ($request->file('after_images') as $image) {
                    $path = $image->store('pest_controls/images', 'public');
                    PestControlImage::create(['pest_control_id' => $pestControl->id, 'type' => 'after', 'image' => $path]);
                }
            }
            // فيديو قبل المبيدات (uppy)
            if ($request->filled('uppy_video_before')) {
                PestControlVideo::create(['pest_control_id' => $pestControl->id, 'type' => 'before', 'video' => $request->uppy_video_before]);
            }
            // فيديو بعد المبيدات (uppy)
            if ($request->filled('uppy_video_after')) {
                PestControlVideo::create(['pest_control_id' => $pestControl->id, 'type' => 'after', 'video' => $request->uppy_video_after]);
            }
        });
        return redirect()->route('dashboard.pest_controls.index')->with('success', __('Pest control created successfully.'));
    }

    public function edit(PestControl $pestControl)
    {
        $cleaners = Cleaner::all();
        $chalets = Chalet::all();
        $pestControl->load(['images', 'videos']);
        return view('dashboard.pest_controls.edit', compact('pestControl', 'cleaners', 'chalets'));
    }

    public function update(Request $request, PestControl $pestControl)
    {
        $validated = $request->validate([
            'cleaner_id' => 'nullable|exists:cleaners,id',
            'chalet_id' => 'nullable|exists:chalets,id',
            'date' => 'required|date',
            'description' => 'nullable|string',
            'status' => 'required|in:pending,done',
            'notes' => 'nullable|string',
            'before_images.*' => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
            'after_images.*' => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
        ]);
        DB::transaction(function () use ($request, $pestControl, $validated) {
            $pestControl->update($validated);
            // صور قبل المبيدات
            if ($request->hasFile('before_images')) {
                foreach ($request->file('before_images') as $image) {
                    $path = $image->store('pest_controls/images', 'public');
                    PestControlImage::create(['pest_control_id' => $pestControl->id, 'type' => 'before', 'image' => $path]);
                }
            }
            // صور بعد المبيدات
            if ($request->hasFile('after_images')) {
                foreach ($request->file('after_images') as $image) {
                    $path = $image->store('pest_controls/images', 'public');
                    PestControlImage::create(['pest_control_id' => $pestControl->id, 'type' => 'after', 'image' => $path]);
                }
            }
            // فيديو قبل المبيدات (uppy)
            if ($request->filled('uppy_video_before')) {
                PestControlVideo::create(['pest_control_id' => $pestControl->id, 'type' => 'before', 'video' => $request->uppy_video_before]);
            }
            // فيديو بعد المبيدات (uppy)
            if ($request->filled('uppy_video_after')) {
                PestControlVideo::create(['pest_control_id' => $pestControl->id, 'type' => 'after', 'video' => $request->uppy_video_after]);
            }
        });
        return redirect()->route('dashboard.pest_controls.index')->with('success', __('Pest control updated successfully.'));
    }

    public function destroy(PestControl $pestControl)
    {
        DB::transaction(function () use ($pestControl) {
            $pestControl->images()->delete();
            $pestControl->videos()->delete();
            $pestControl->delete();
        });
        return redirect()->route('dashboard.pest_controls.index')->with('success', __('Pest control deleted successfully.'));
    }

    public function uploadVideo(Request $request)
    {
        if ($request->hasFile('video')) {
            $path = $request->file('video')->store('pest_controls/videos', 'public');
            return response()->json(['path' => $path], 200);
        }
        return response()->json(['error' => 'No video uploaded'], 400);
    }
}
