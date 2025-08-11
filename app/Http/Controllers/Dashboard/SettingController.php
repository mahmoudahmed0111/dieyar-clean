<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::first();
        return view('dashboard.settings.index', compact('settings'));
    }

    public function update(Request $request, Setting $setting)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'facebook' => 'nullable|string|max:255',
            'instagram' => 'nullable|string|max:255',
            'twitter' => 'nullable|string|max:255',
            'whatsapp' => 'nullable|string|max:255',
        ]);
        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('settings', 'public');
        }
        $setting->update($validated);
        session()->flash('success', __('Settings updated successfully.'));
        return redirect()->route('dashboard.settings.index');
    }

}
