<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Cleaner;
use App\Services\FirebaseNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class TestNotificationController extends Controller
{
    use ResponseTrait;

    /**
     * إرسال إشعار تجريبي
     */
    public function sendTestNotification(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:100',
                'body' => 'required|string|max:500',
                'cleaner_id' => 'nullable|exists:cleaners,id',
            ], [
                'title.required' => 'عنوان الإشعار مطلوب',
                'title.string' => 'عنوان الإشعار يجب أن يكون نص',
                'title.max' => 'عنوان الإشعار يجب أن يكون أقل من 100 حرف',
                'body.required' => 'محتوى الإشعار مطلوب',
                'body.string' => 'محتوى الإشعار يجب أن يكون نص',
                'body.max' => 'محتوى الإشعار يجب أن يكون أقل من 500 حرف',
                'cleaner_id.exists' => 'عامل النظافة غير موجود',
            ]);

            if ($validator->fails()) {
                return $this->apiResponse(null, $validator->errors()->first(), 422);
            }

            $firebaseService = new FirebaseNotificationService();
            
            $data = [
                'type' => 'test',
                'test_id' => time(),
                'sent_at' => now()->toISOString(),
            ];

            if ($request->cleaner_id) {
                // إرسال لعامل نظافة محدد
                $cleaner = Cleaner::find($request->cleaner_id);
                $result = $firebaseService->sendToCleaner(
                    $cleaner,
                    $request->title,
                    $request->body,
                    $data
                );
                
                if ($result) {
                    return $this->apiResponse([
                        'sent_to' => 'specific_cleaner',
                        'cleaner_id' => $cleaner->id,
                        'cleaner_name' => $cleaner->name,
                    ], 'تم إرسال الإشعار التجريبي بنجاح');
                } else {
                    return $this->apiResponse(null, 'فشل في إرسال الإشعار التجريبي', 500);
                }
            } else {
                // إرسال لجميع عمال النظافة
                $result = $firebaseService->sendToAllCleaners(
                    $request->title,
                    $request->body,
                    $data
                );
                
                if ($result) {
                    return $this->apiResponse([
                        'sent_to' => 'all_cleaners',
                        'message' => 'تم إرسال الإشعار لجميع عمال النظافة',
                    ], 'تم إرسال الإشعار التجريبي بنجاح');
                } else {
                    return $this->apiResponse(null, 'فشل في إرسال الإشعار التجريبي', 500);
                }
            }

        } catch (\Exception $e) {
            Log::error('Error in sendTestNotification: ' . $e->getMessage());
            return $this->apiResponse(null, 'حدث خطأ أثناء إرسال الإشعار التجريبي: ' . $e->getMessage(), 500);
        }
    }

    /**
     * اختبار إعدادات Firebase
     */
    public function testFirebaseConfig()
    {
        try {
            $firebaseService = new FirebaseNotificationService();
            
            // التحقق من وجود ملف Firebase
            $firebaseConfigPath = config('services.firebase.service_account_path');
            $configExists = file_exists($firebaseConfigPath);
            
            // التحقق من إعدادات Firebase
            $projectId = config('services.firebase.project_id');
            $serverKey = config('services.firebase.server_key');
            
            // جلب عدد عمال النظافة الذين لديهم FCM tokens
            $cleanersWithTokens = Cleaner::where('status', 'active')
                ->whereNotNull('fcm_token')
                ->count();
            
            $totalCleaners = Cleaner::where('status', 'active')->count();
            
            return $this->apiResponse([
                'firebase_config' => [
                    'config_file_exists' => $configExists,
                    'config_file_path' => $firebaseConfigPath,
                    'project_id' => $projectId,
                    'server_key_configured' => !empty($serverKey),
                ],
                'cleaners_stats' => [
                    'total_active_cleaners' => $totalCleaners,
                    'cleaners_with_fcm_tokens' => $cleanersWithTokens,
                    'cleaners_without_fcm_tokens' => $totalCleaners - $cleanersWithTokens,
                ],
                'status' => $configExists && !empty($projectId) && !empty($serverKey) ? 'ready' : 'not_ready',
            ], 'تم فحص إعدادات Firebase بنجاح');
            
        } catch (\Exception $e) {
            Log::error('Error in testFirebaseConfig: ' . $e->getMessage());
            return $this->apiResponse(null, 'حدث خطأ أثناء فحص إعدادات Firebase: ' . $e->getMessage(), 500);
        }
    }
}
