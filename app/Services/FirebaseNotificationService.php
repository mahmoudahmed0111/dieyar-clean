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
        // قراءة إعدادات Firebase من ملف الإعدادات
        $this->projectId = config('services.firebase.project_id', 'deiyar');
        $this->serverKey = config('services.firebase.server_key');
        
        // التحقق من وجود ملف Firebase
        $firebaseConfigPath = config('services.firebase.service_account_path');
        
        if (!file_exists($firebaseConfigPath)) {
            Log::warning('Firebase service account file not found at: ' . $firebaseConfigPath);
        } else {
            Log::info('Firebase service account file loaded successfully');
        }
    }

    /**
     * إرسال إشعار لجميع عمال النظافة
     */
    public function sendToAllCleaners($title, $body, $data = [], $excludeCleanerId = null)
    {
        // جلب جميع عمال النظافة النشطين الذين لديهم FCM token
        $cleaners = Cleaner::where('status', 'active')
            ->whereNotNull('fcm_token')
            ->when($excludeCleanerId, function ($query) use ($excludeCleanerId) {
                return $query->where('id', '!=', $excludeCleanerId);
            })
            ->get();

        if ($cleaners->isEmpty()) {
            Log::info('No active cleaners with FCM tokens found');
            return false;
        }

        $successCount = 0;
        $failCount = 0;

        foreach ($cleaners as $cleaner) {
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
                $successCount++;
                Log::info("Notification sent successfully to cleaner: {$cleaner->id}");
            } else {
                $failCount++;
                Log::error("Failed to send notification to cleaner: {$cleaner->id}", [
                    'response' => $response ? $response->body() : 'No response'
                ]);
            }
        }

        Log::info("Notification broadcast completed. Success: {$successCount}, Failed: {$failCount}");
        return $successCount > 0;
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
        // التحقق من وجود إعدادات Firebase
        if (!$this->serverKey) {
            Log::warning('Firebase server key not configured. Skipping FCM notification.');
            return null;
        }

        // استخدام Firebase Cloud Messaging API v1
        $url = "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send";

        $payload = [
            'message' => [
                'token' => $token,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                ],
                'data' => array_merge($data, [
                    'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                    'sound' => 'default',
                ]),
                'android' => [
                    'priority' => 'high',
                    'notification' => [
                        'sound' => 'default',
                        'priority' => 'high',
                    ],
                ],
                'apns' => [
                    'payload' => [
                        'aps' => [
                            'sound' => 'default',
                            'badge' => 1,
                        ],
                    ],
                ],
            ],
        ];

        try {
            // الحصول على access token من Firebase
            $accessToken = $this->getAccessToken();
            
            if (!$accessToken) {
                Log::error('Failed to get Firebase access token');
                return null;
            }

            return Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
            ])->post($url, $payload);
        } catch (\Exception $e) {
            Log::error('FCM notification error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * الحصول على access token من Firebase
     */
    private function getAccessToken()
    {
        try {
            $firebaseConfigPath = config('services.firebase.service_account_path');
            
            if (!file_exists($firebaseConfigPath)) {
                Log::error('Firebase service account file not found');
                return null;
            }

            $serviceAccount = json_decode(file_get_contents($firebaseConfigPath), true);
            
            $payload = [
                'iss' => $serviceAccount['client_email'],
                'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
                'aud' => 'https://oauth2.googleapis.com/token',
                'iat' => time(),
                'exp' => time() + 3600,
            ];

            $header = [
                'alg' => 'RS256',
                'typ' => 'JWT',
            ];

            $headerEncoded = $this->base64url_encode(json_encode($header));
            $payloadEncoded = $this->base64url_encode(json_encode($payload));
            
            $signature = '';
            $privateKey = $serviceAccount['private_key'];
            
            openssl_sign(
                $headerEncoded . '.' . $payloadEncoded,
                $signature,
                $privateKey,
                'SHA256'
            );
            
            $signatureEncoded = $this->base64url_encode($signature);
            $jwt = $headerEncoded . '.' . $payloadEncoded . '.' . $signatureEncoded;

            $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwt,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['access_token'] ?? null;
            }

            Log::error('Failed to get access token: ' . $response->body());
            return null;
        } catch (\Exception $e) {
            Log::error('Error getting Firebase access token: ' . $e->getMessage());
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

    /**
     * دالة مساعدة لترميز base64url
     */
    private function base64url_encode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}
