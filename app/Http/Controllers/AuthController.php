<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // التحقق من البيانات
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:6',
        ], [
            'email.required' => 'البريد الإلكتروني مطلوب',
            'email.email' => 'البريد الإلكتروني غير صحيح',
            'password.required' => 'كلمة المرور مطلوبة',
            'password.min' => 'كلمة المرور يجب أن تكون 6 أحرف على الأقل',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->except('password'));
        }

        // محاولة تسجيل الدخول
        $credentials = $request->only('email', 'password');
        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            // رسالة نجاح
            session()->flash('success', 'تم تسجيل الدخول بنجاح!');

            return redirect()->intended('/dashboard');
        }

        // فشل تسجيل الدخول
        return redirect()->back()
            ->withErrors([
                'email' => 'البريد الإلكتروني أو كلمة المرور غير صحيحة',
            ])
            ->withInput($request->except('password'));
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        session()->flash('success', 'تم تسجيل الخروج بنجاح!');

        return redirect('/');
    }

    public function dashboard()
    {
        $stats = [
            'users' => \App\Models\User::count(),
            'cleaners' => \App\Models\Cleaner::count(),
            'chalets' => \App\Models\Chalet::count(),
            'damages' => \App\Models\Damage::count(),
            'deep_cleanings' => \App\Models\DeepCleaning::count(),
            'regular_cleanings' => \App\Models\RegularCleaning::count(),
            'maintenance' => \App\Models\Maintenance::count(),
            'pest_controls' => \App\Models\PestControl::count(),
            'inventory' => \App\Models\Inventory::count(),
            // إحصائيات إضافية
            'damages_pending' => \App\Models\Damage::where('status','pending')->count(),
            'damages_fixed' => \App\Models\Damage::where('status','fixed')->count(),
            'maintenance_pending' => \App\Models\Maintenance::where('status','pending')->count(),
            'maintenance_done' => \App\Models\Maintenance::where('status','done')->count(),
            'deep_cleanings_this_month' => \App\Models\DeepCleaning::whereMonth('date', now()->month)->count(),
            'regular_cleanings_this_month' => \App\Models\RegularCleaning::whereMonth('date', now()->month)->count(),
        ];
        return view('dashboard.index', compact('stats'));
    }
}
