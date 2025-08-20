<?php

namespace App\Observers;

use App\Models\RegularCleaning;
use App\Services\FirebaseNotificationService;

class RegularCleaningObserver
{
    protected $firebaseService;

    public function __construct(FirebaseNotificationService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    /**
     * Handle the RegularCleaning "created" event.
     */
    public function created(RegularCleaning $regularCleaning): void
    {
        // إرسال إشعار عند إنشاء مهمة تنظيف منتظم جديدة
        $this->firebaseService->sendRegularCleaningNotification($regularCleaning);
    }

    /**
     * Handle the RegularCleaning "updated" event.
     */
    public function updated(RegularCleaning $regularCleaning): void
    {
        // يمكن إضافة منطق إضافي هنا إذا لزم الأمر
    }

    /**
     * Handle the RegularCleaning "deleted" event.
     */
    public function deleted(RegularCleaning $regularCleaning): void
    {
        // يمكن إضافة منطق إضافي هنا إذا لزم الأمر
    }
}

