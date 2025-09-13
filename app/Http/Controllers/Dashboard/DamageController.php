<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Damage;
use App\Models\DamageImage;
use App\Models\DamageVideo;
use App\Models\Cleaner;
use App\Models\Chalet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DamageController extends Controller
{
    public function index()
    {
        $damages = Damage::with(['images', 'videos', 'cleaner', 'chalet'])->orderByDesc('created_at')->paginate(10);
        return view('dashboard.damages.index', compact('damages'));
    }

    public function create()
    {
        $cleaners = Cleaner::all();
        $chalets = Chalet::all();
        return view('dashboard.damages.create', compact('cleaners', 'chalets'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'cleaner_id' => 'nullable|exists:cleaners,id',
            'chalet_id' => 'nullable|exists:chalets,id',
            'description' => 'required|string',
            'price' => 'required|string',
            'reported_at' => 'nullable|date',
            'status' => 'required|in:pending,fixed',
            'images.*' => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
        ]);
        DB::transaction(function () use ($request, $validated) {
            $damage = Damage::create($validated);
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $path = $image->store('damages/images', 'public');
                    DamageImage::create(['damage_id' => $damage->id, 'image' => $path]);
                }
            }
            // حفظ الفيديو المرفوع عبر Uppy
            if ($request->filled('uppy_video')) {
                DamageVideo::create(['damage_id' => $damage->id, 'video' => $request->uppy_video]);
            }
        });
        return redirect()->route('dashboard.damages.index')->with('success', __('Damage reported successfully.'));
    }

    public function edit(Damage $damage)
    {
        $cleaners = Cleaner::all();
        $chalets = Chalet::all();
        $damage->load(['images', 'videos']);
        return view('dashboard.damages.edit', compact('damage', 'cleaners', 'chalets'));
    }

    public function update(Request $request, Damage $damage)
    {
        $validated = $request->validate([
            'cleaner_id' => 'nullable|exists:cleaners,id',
            'chalet_id' => 'nullable|exists:chalets,id',
            'description' => 'required|string',
            'price' => 'required|string',
            'reported_at' => 'nullable|date',
            'status' => 'required|in:pending,fixed',
            'images.*' => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
        ]);
        DB::transaction(function () use ($request, $damage, $validated) {
            $damage->update($validated);
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $path = $image->store('damages/images', 'public');
                    DamageImage::create(['damage_id' => $damage->id, 'image' => $path]);
                }
            }
            if ($request->filled('uppy_video')) {
                DamageVideo::create(['damage_id' => $damage->id, 'video' => $request->uppy_video]);
            }
        });
        return redirect()->route('dashboard.damages.index')->with('success', __('Damage updated successfully.'));
    }

    public function destroy(Damage $damage)
    {
        try {
            DB::transaction(function () use ($damage) {
                $damage->images()->delete();
                $damage->videos()->delete();
                $damage->delete();
            });
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => __('Damage deleted successfully.')
                ]);
            }
            
            return redirect()->route('dashboard.damages.index')->with('success', __('Damage deleted successfully.'));
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'حدث خطأ أثناء الحذف: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->route('dashboard.damages.index')->with('error', 'حدث خطأ أثناء الحذف: ' . $e->getMessage());
        }
    }

    public function uploadVideo(Request $request)
    {
        if ($request->hasFile('video')) {
            $path = $request->file('video')->store('damages/videos', 'public');
            return response()->json(['path' => $path], 200);
        }
        return response()->json(['error' => 'No video uploaded'], 400);
    }
}
