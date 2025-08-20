<?php

namespace App\Observers;

use App\Models\Damage;
use App\Services\FirebaseNotificationService;

class DamageObserver
{
    protected $firebaseService;

    public function __construct(FirebaseNotificationService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    /**
     * Handle the Damage "created" event.
     */
    public function created(Damage $damage): void
    {
        // إرسال إشعار عند إنشاء بلاغ أضرار جديد
        $this->firebaseService->sendDamageNotification($damage);
    }

    /**
     * Handle the Damage "updated" event.
     */
    public function updated(Damage $damage): void
    {
        // يمكن إضافة منطق إضافي هنا إذا لزم الأمر
    }

    /**
     * Handle the Damage "deleted" event.
     */
    public function deleted(Damage $damage): void
    {
        // يمكن إضافة منطق إضافي هنا إذا لزم الأمر
    }
}

