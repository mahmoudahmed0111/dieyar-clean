<?php

namespace App\Providers;

use App\Models\Setting;
use App\Models\RegularCleaning;
use App\Models\DeepCleaning;
use App\Models\Maintenance;
use App\Models\PestControl;
use App\Models\Damage;
use App\Observers\RegularCleaningObserver;
use App\Observers\DeepCleaningObserver;
use App\Observers\MaintenanceObserver;
use App\Observers\PestControlObserver;
use App\Observers\DamageObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // التحقق من وجود جدول settings قبل محاولة الوصول إليه
        try {
            $settings = Setting::first();
            View::share('settings', $settings);
        } catch (\Exception $e) {
            // إذا لم يكن الجدول موجود، استخدم إعدادات افتراضية
            View::share('settings', null);
        }

        // تسجيل Observers للإشعارات
        RegularCleaning::observe(RegularCleaningObserver::class);
        DeepCleaning::observe(DeepCleaningObserver::class);
        Maintenance::observe(MaintenanceObserver::class);
        PestControl::observe(PestControlObserver::class);
        Damage::observe(DamageObserver::class);
    }
}
