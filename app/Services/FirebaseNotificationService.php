<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Cleaner;
use App\Jobs\SendNotificationJob;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FirebaseNotificationService
{
    private $serverKey;
    private $projectId;

    public function __construct()
    {
        // قراءة بيانات Firebase من ملف JSON
        $firebaseConfig = json_decode(file_get_contents(storage_path('app/firebase-service-account.json')), true);
        $this->projectId = $firebaseConfig['project_id'];
        $this->serverKey = config('services.firebase.server_key');
    }

    /**
     * إرسال إشعار لجميع عمال النظافة
     */
    public function sendToAllCleaners($title, $body, $data = [])
    {
        // استخدام Job لإرسال الإشعارات في الخلفية
        SendNotificationJob::dispatch($title, $body, $data);
    }

    /**
     * إرسال إشعار لعامل نظافة محدد
     */
    public function sendToCleaner(Cleaner $cleaner, $title, $body, $data = [])
    {
        if (!$cleaner->fcm_token) {
            Log::warning("No FCM token for cleaner: {$cleaner->id}");
            return false;
        }

        $notification = Notification::create([
            'cleaner_id' => $cleaner->id,
            'title' => $title,
            'body' => $body,
            'type' => $data['type'] ?? 'general',
            'data' => $data,
            'fcm_token' => $cleaner->fcm_token,
        ]);

        $response = $this->sendFCMNotification($cleaner->fcm_token, $title, $body, $data);

        if ($response && $response->successful()) {
            $notification->markAsSent();
            Log::info("Notification sent successfully to cleaner: {$cleaner->id}");
            return true;
        } else {
            Log::error("Failed to send notification to cleaner: {$cleaner->id}", [
                'response' => $response ? $response->body() : 'No response'
            ]);
            return false;
        }
    }

    /**
     * إرسال إشعار عبر FCM
     */
    private function sendFCMNotification($token, $title, $body, $data = [])
    {
        $url = "https://fcm.googleapis.com/fcm/send";

        $payload = [
            'to' => $token,
            'notification' => [
                'title' => $title,
                'body' => $body,
                'sound' => 'default',
                'badge' => 1,
            ],
            'data' => array_merge($data, [
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                'sound' => 'default',
            ]),
            'priority' => 'high',
        ];

        try {
            return Http::withHeaders([
                'Authorization' => 'key=' . $this->serverKey,
                'Content-Type' => 'application/json',
            ])->post($url, $payload);
        } catch (\Exception $e) {
            Log::error('FCM notification error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * إرسال إشعار تنظيف منتظم
     */
    public function sendRegularCleaningNotification($cleaning)
    {
        $title = 'تنظيف منتظم جديد';
        $body = "تم إنشاء مهمة تنظيف منتظم للشاليه: {$cleaning->chalet->name}";

        $data = [
            'type' => 'regular_cleaning',
            'cleaning_id' => $cleaning->id,
            'chalet_id' => $cleaning->chalet_id,
            'chalet_name' => $cleaning->chalet->name,
        ];

        return $this->sendToAllCleaners($title, $body, $data);
    }

    /**
     * إرسال إشعار تنظيف عميق
     */
    public function sendDeepCleaningNotification($cleaning)
    {
        $title = 'تنظيف عميق جديد';
        $body = "تم إنشاء مهمة تنظيف عميق للشاليه: {$cleaning->chalet->name}";

        $data = [
            'type' => 'deep_cleaning',
            'cleaning_id' => $cleaning->id,
            'chalet_id' => $cleaning->chalet_id,
            'chalet_name' => $cleaning->chalet->name,
        ];

        return $this->sendToAllCleaners($title, $body, $data);
    }

    /**
     * إرسال إشعار صيانة
     */
    public function sendMaintenanceNotification($maintenance)
    {
        $title = 'مهمة صيانة جديدة';
        $body = "تم إنشاء مهمة صيانة للشاليه: {$maintenance->chalet->name}";

        $data = [
            'type' => 'maintenance',
            'maintenance_id' => $maintenance->id,
            'chalet_id' => $maintenance->chalet_id,
            'chalet_name' => $maintenance->chalet->name,
        ];

        return $this->sendToAllCleaners($title, $body, $data);
    }

    /**
     * إرسال إشعار مكافحة آفات
     */
    public function sendPestControlNotification($pestControl)
    {
        $title = 'مهمة مكافحة آفات جديدة';
        $body = "تم إنشاء مهمة مكافحة آفات للشاليه: {$pestControl->chalet->name}";

        $data = [
            'type' => 'pest_control',
            'pest_control_id' => $pestControl->id,
            'chalet_id' => $pestControl->chalet_id,
            'chalet_name' => $pestControl->chalet->name,
        ];

        return $this->sendToAllCleaners($title, $body, $data);
    }

    /**
     * إرسال إشعار أضرار
     */
    public function sendDamageNotification($damage)
    {
        $title = 'بلاغ أضرار جديد';
        $body = "تم تسجيل بلاغ أضرار للشاليه: {$damage->chalet->name}";

        $data = [
            'type' => 'damage',
            'damage_id' => $damage->id,
            'chalet_id' => $damage->chalet_id,
            'chalet_name' => $damage->chalet->name,
        ];

        return $this->sendToAllCleaners($title, $body, $data);
    }
}
