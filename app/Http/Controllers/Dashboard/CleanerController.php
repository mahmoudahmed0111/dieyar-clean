<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Cleaner;
use Illuminate\Http\Request;

class CleanerController extends Controller
{
    public function index(Request $request)
    {
        $query = Cleaner::query();
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('national_id', 'like', "%{$search}%");
            });
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        $cleaners = $query->orderByDesc('created_at')->paginate(10);
        return view('dashboard.cleaners.index', compact('cleaners'));
    }

    public function create()
    {
        return view('dashboard.cleaners.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'national_id' => 'required|string|max:50',
            'address' => 'required|string|max:255',
            'hire_date' => 'required|date',
            'status' => 'required|in:active,inactive',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('cleaners', 'public');
        }
        Cleaner::create($validated);
        return redirect()->route('dashboard.cleaners.index')->with('success', __('Cleaner created successfully.'));
    }

    public function edit(Cleaner $cleaner)
    {
        return view('dashboard.cleaners.edit', compact('cleaner'));
    }

    public function update(Request $request, Cleaner $cleaner)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'national_id' => 'required|string|max:50',
            'address' => 'required|string|max:255',
            'hire_date' => 'required|date',
            'status' => 'required|in:active,inactive',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('cleaners', 'public');
        }
        $cleaner->update($validated);
        return redirect()->route('dashboard.cleaners.index')->with('success', __('Cleaner updated successfully.'));
    }

    public function destroy(Cleaner $cleaner)
    {
        $cleaner->delete();
        return redirect()->route('dashboard.cleaners.index')->with('success', __('Cleaner deleted successfully.'));
    }

    public function toggleStatus(Cleaner $cleaner)
    {
        $cleaner->status = $cleaner->status === 'active' ? 'inactive' : 'active';
        $cleaner->save();
        return redirect()->route('dashboard.cleaners.index')->with('success', __('Cleaner status updated.'));
    }
}
