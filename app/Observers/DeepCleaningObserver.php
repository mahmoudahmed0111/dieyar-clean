<?php

namespace App\Observers;

use App\Models\DeepCleaning;
use App\Services\FirebaseNotificationService;

class DeepCleaningObserver
{
    protected $firebaseService;

    public function __construct(FirebaseNotificationService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    /**
     * Handle the DeepCleaning "created" event.
     */
    public function created(DeepCleaning $deepCleaning): void
    {
        // إرسال إشعار عند إنشاء مهمة تنظيف عميق جديدة
        $this->firebaseService->sendDeepCleaningNotification($deepCleaning);
    }

    /**
     * Handle the DeepCleaning "updated" event.
     */
    public function updated(DeepCleaning $deepCleaning): void
    {
        // يمكن إضافة منطق إضافي هنا إذا لزم الأمر
    }

    /**
     * Handle the DeepCleaning "deleted" event.
     */
    public function deleted(DeepCleaning $deepCleaning): void
    {
        // يمكن إضافة منطق إضافي هنا إذا لزم الأمر
    }
}

