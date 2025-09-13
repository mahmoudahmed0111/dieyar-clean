<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Chalet;
use App\Models\ChaletImage;
use App\Models\ChaletVideo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChaletController extends Controller
{
    public function index()
    {
        $chalets = Chalet::with(['images', 'videos'])->orderByDesc('created_at')->paginate(10);
        return view('dashboard.chalets.index', compact('chalets'));
    }

    public function create()
    {
        return view('dashboard.chalets.create');
    }

    public function store(Request $request)
    {
        // معالجة البيانات المرسلة من النماذج
        $data = $request->all();

        // معالجة الحقول boolean
        $data['is_cleaned'] = $request->has('is_cleaned') ? true : false;
        $data['is_booked'] = $request->has('is_booked') ? true : false;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:chalets,code',
            'pass_code' => 'required|string|max:50',
            'floor' => 'nullable|string|max:100',
            'building' => 'nullable|string|max:100',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:available,unavailable',
            'type' => 'nullable|in:apartment,studio,villa',
            'is_cleaned' => 'boolean',
            'is_booked' => 'boolean',
            'images.*' => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
            'videos.*' => 'nullable|file|mimes:mp4,avi,mov|max:20480',
        ]);

        // دمج البيانات المعالجة مع البيانات المصدقة
        $validated['is_cleaned'] = $data['is_cleaned'];
        $validated['is_booked'] = $data['is_booked'];

        try {
            $chalet = DB::transaction(function () use ($request, $validated) {
                $chalet = Chalet::create($validated);

                if ($request->hasFile('images')) {
                    foreach ($request->file('images') as $image) {
        $path = $image->store('chalets/images', 'public');
                        ChaletImage::create(['chalet_id' => $chalet->id, 'image' => $path]);
                    }
                }

// حفظ الفيديو المرفوع عبر Uppy
                if ($request->filled('uppy_video')) {
                    ChaletVideo::create(['chalet_id' => $chalet->id, 'video' => $request->uppy_video]);
                }

                return $chalet;
            });

            return redirect()->route('dashboard.chalets.index')->with('success', __('trans.chalet_created_successfully'));
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => 'حدث خطأ أثناء إنشاء الشاليه: ' . $e->getMessage()]);
        }
    }

    public function show(Chalet $chalet)
    {
        $chalet->load(['images', 'videos']);
        return view('dashboard.chalets.show', compact('chalet'));
    }

    public function edit(Chalet $chalet)
    {
        $chalet->load(['images', 'videos']);
        return view('dashboard.chalets.edit', compact('chalet'));
    }

    public function update(Request $request, Chalet $chalet)
    {
        // معالجة البيانات المرسلة من النماذج
        $data = $request->all();

        // معالجة الحقول boolean
        $data['is_cleaned'] = $request->has('is_cleaned') ? true : false;
        $data['is_booked'] = $request->has('is_booked') ? true : false;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:chalets,code,' . $chalet->id,
            'pass_code' => 'required|string|max:50',
            'floor' => 'nullable|string|max:100',
            'building' => 'nullable|string|max:100',
            'location' => 'nullable|string|max:255',
            'type' => 'nullable|in:apartment,studio,villa',
            'status' => 'required|in:available,unavailable',
            'description' => 'nullable|string',
            'is_cleaned' => 'boolean',
            'is_booked' => 'boolean',
            'images.*' => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
        ]);

        // دمج البيانات المعالجة مع البيانات المصدقة
        $validated['is_cleaned'] = $data['is_cleaned'];
        $validated['is_booked'] = $data['is_booked'];

        try {
            DB::transaction(function () use ($request, $validated, $chalet) {
                $chalet->update($validated);

                // إضافة صور جديدة
                if ($request->hasFile('images')) {
                    foreach ($request->file('images') as $image) {
        $path = $image->store('chalets/images', 'public');
                        $chalet->images()->create(['image' => $path]);
                    }
                }

                // إضافة فيديو جديد إذا تم رفعه
                if ($request->filled('uppy_video')) {
                    // إذا لم يكن هناك فيديو سابق أو تم رفع فيديو جديد
                    if ($chalet->videos()->count() == 0 || $request->filled('uppy_video')) {
                        $chalet->videos()->create(['video' => $request->uppy_video]);
                    }
                }
            });

            return redirect()->route('dashboard.chalets.index')->with('success', __('trans.chalet_updated_successfully'));
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => 'حدث خطأ أثناء تحديث الشاليه: ' . $e->getMessage()]);
        }
    }

    public function destroy(Chalet $chalet)
    {
        try {
            DB::transaction(function () use ($chalet) {
                $chalet->images()->delete();
                $chalet->videos()->delete();
                $chalet->delete();
            });
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => __('trans.chalet_deleted_successfully')
                ]);
            }
            
            return redirect()->route('dashboard.chalets.index')->with('success', __('trans.chalet_deleted_successfully'));
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'حدث خطأ أثناء الحذف: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->route('dashboard.chalets.index')->with('error', 'حدث خطأ أثناء الحذف: ' . $e->getMessage());
        }
    }

    public function uploadVideo(Request $request)
    {
        if ($request->hasFile('video')) {
            $path = $request->file('video')->store('chalets/videos', 'public');
            return response()->json(['path' => $path], 200);
        }
        return response()->json(['error' => 'No video uploaded'], 400);
    }
}
