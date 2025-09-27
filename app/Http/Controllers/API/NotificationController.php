<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Cleaner;
use App\Services\FirebaseNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\API\ResponseTrait;

class NotificationController extends Controller
{
    use ResponseTrait;

    protected $firebaseService;

    public function __construct(FirebaseNotificationService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    /**
     * عرض جميع إشعارات عامل النظافة
     */
    public function index(Request $request)
    {
        try {
            $cleaner = Auth::user();

            $notifications = Notification::where('cleaner_id', $cleaner->id)
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            // إضافة متغير read لكل إشعار
            $notificationsWithReadStatus = $notifications->items();
            foreach ($notificationsWithReadStatus as $notification) {
                $notification->read = !is_null($notification->read_at);
            }

            return $this->apiResponse([
                'notifications' => $notificationsWithReadStatus,
                'pagination' => [
                    'current_page' => $notifications->currentPage(),
                    'last_page' => $notifications->lastPage(),
                    'per_page' => $notifications->perPage(),
                    'total' => $notifications->total(),
                ]
            ], 'تم جلب الإشعارات بنجاح');
        } catch (\Exception $e) {
            return $this->apiResponse(null, 'حدث خطأ أثناء جلب الإشعارات', 500);
        }
    }

    /**
     * عرض إشعار محدد
     */
    public function show($id)
    {
        try {
            $cleaner = Auth::user();

            $notification = Notification::where('cleaner_id', $cleaner->id)
                ->where('id', $id)
                ->first();

            if (!$notification) {
                return $this->apiResponse(null, 'الإشعار غير موجود', 404);
            }

            // تحديد الإشعار كمقروء
            if (!$notification->read_at) {
                $notification->markAsRead();
            }

            return $this->apiResponse($notification, 'تم جلب الإشعار بنجاح');
        } catch (\Exception $e) {
            return $this->apiResponse(null, 'حدث خطأ أثناء جلب الإشعار', 500);
        }
    }

    /**
     * تحديد إشعار كمقروء
     */
    public function markAsRead($id)
    {
        try {
            $cleaner = Auth::user();

            $notification = Notification::where('cleaner_id', $cleaner->id)
                ->where('id', $id)
                ->first();

            if (!$notification) {
                return $this->apiResponse(null, 'الإشعار غير موجود', 404);
            }

            $notification->markAsRead();

            return $this->apiResponse(null, 'تم تحديد الإشعار كمقروء بنجاح');
        } catch (\Exception $e) {
            return $this->apiResponse(null, 'حدث خطأ أثناء تحديد الإشعار كمقروء', 500);
        }
    }

    /**
     * تحديد جميع الإشعارات كمقروءة
     */
    public function markAllAsRead()
    {
        try {
            $cleaner = Auth::user();

            Notification::where('cleaner_id', $cleaner->id)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);

            return $this->apiResponse(null, 'تم تحديد جميع الإشعارات كمقروءة بنجاح');
        } catch (\Exception $e) {
            return $this->apiResponse(null, 'حدث خطأ أثناء تحديد الإشعارات كمقروءة', 500);
        }
    }

    /**
     * حذف إشعار
     */
    public function destroy($id)
    {
        try {
            $cleaner = Auth::user();

            $notification = Notification::where('cleaner_id', $cleaner->id)
                ->where('id', $id)
                ->first();

            if (!$notification) {
                return $this->apiResponse(null, 'الإشعار غير موجود', 404);
            }

            $notification->delete();

            return $this->apiResponse(null, 'تم حذف الإشعار بنجاح');
        } catch (\Exception $e) {
            return $this->apiResponse(null, 'حدث خطأ أثناء حذف الإشعار', 500);
        }
    }

    /**
     * حذف جميع الإشعارات
     */
    public function destroyAll()
    {
        try {
            $cleaner = Auth::user();

            Notification::where('cleaner_id', $cleaner->id)->delete();

            return $this->apiResponse(null, 'تم حذف جميع الإشعارات بنجاح');
        } catch (\Exception $e) {
            return $this->apiResponse(null, 'حدث خطأ أثناء حذف الإشعارات', 500);
        }
    }

    /**
     * تحديث رمز FCM للجهاز
     */
    public function updateFcmToken(Request $request)
    {
        try {
            $request->validate([
                'fcm_token' => 'required|string'
            ]);

            $cleaner = Auth::user();
            $cleaner->update(['fcm_token' => $request->fcm_token]);

            return $this->apiResponse(null, 'تم تحديث رمز الجهاز بنجاح');
        } catch (\Exception $e) {
            return $this->apiResponse(null, 'حدث خطأ أثناء تحديث رمز الجهاز', 500);
        }
    }

    /**
     * إحصائيات الإشعارات
     */
    public function stats()
    {
        try {
            $cleaner = Auth::user();

            $totalNotifications = Notification::where('cleaner_id', $cleaner->id)->count();
            $unreadNotifications = Notification::where('cleaner_id', $cleaner->id)
                ->whereNull('read_at')
                ->count();
            $readNotifications = $totalNotifications - $unreadNotifications;

            return $this->apiResponse([
                'total' => $totalNotifications,
                'unread' => $unreadNotifications,
                'read' => $readNotifications
            ], 'تم جلب إحصائيات الإشعارات بنجاح');
        } catch (\Exception $e) {
            return $this->apiResponse(null, 'حدث خطأ أثناء جلب إحصائيات الإشعارات', 500);
        }
    }

    /**
     * إرسال إشعار تجريبي
     */
    public function sendTestNotification()
    {
        try {
            $cleaner = Auth::user();

            $title = 'إشعار تجريبي';
            $body = 'هذا إشعار تجريبي لاختبار نظام الإشعارات';

            $data = [
                'type' => 'test',
                'timestamp' => now()->toISOString(),
            ];

            $this->firebaseService->sendToCleaner($cleaner, $title, $body, $data);

            return $this->apiResponse(null, 'تم إرسال الإشعار التجريبي بنجاح');
        } catch (\Exception $e) {
            return $this->apiResponse(null, 'حدث خطأ أثناء إرسال الإشعار التجريبي', 500);
        }
    }
}
