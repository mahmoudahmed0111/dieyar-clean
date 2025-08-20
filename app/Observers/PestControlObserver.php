<?php

namespace App\Observers;

use App\Models\PestControl;
use App\Services\FirebaseNotificationService;

class PestControlObserver
{
    protected $firebaseService;

    public function __construct(FirebaseNotificationService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    /**
     * Handle the PestControl "created" event.
     */
    public function created(PestControl $pestControl): void
    {
        // إرسال إشعار عند إنشاء مهمة مكافحة آفات جديدة
        $this->firebaseService->sendPestControlNotification($pestControl);
    }

    /**
     * Handle the PestControl "updated" event.
     */
    public function updated(PestControl $pestControl): void
    {
        // يمكن إضافة منطق إضافي هنا إذا لزم الأمر
    }

    /**
     * Handle the PestControl "deleted" event.
     */
    public function deleted(PestControl $pestControl): void
    {
        // يمكن إضافة منطق إضافي هنا إذا لزم الأمر
    }
}

