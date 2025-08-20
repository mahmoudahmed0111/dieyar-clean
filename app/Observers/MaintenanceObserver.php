<?php

namespace App\Observers;

use App\Models\Maintenance;
use App\Services\FirebaseNotificationService;

class MaintenanceObserver
{
    protected $firebaseService;

    public function __construct(FirebaseNotificationService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    /**
     * Handle the Maintenance "created" event.
     */
    public function created(Maintenance $maintenance): void
    {
        // إرسال إشعار عند إنشاء مهمة صيانة جديدة
        $this->firebaseService->sendMaintenanceNotification($maintenance);
    }

    /**
     * Handle the Maintenance "updated" event.
     */
    public function updated(Maintenance $maintenance): void
    {
        // يمكن إضافة منطق إضافي هنا إذا لزم الأمر
    }

    /**
     * Handle the Maintenance "deleted" event.
     */
    public function deleted(Maintenance $maintenance): void
    {
        // يمكن إضافة منطق إضافي هنا إذا لزم الأمر
    }
}

