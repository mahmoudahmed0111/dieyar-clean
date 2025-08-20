# نظام الإشعارات - Dieyar Clean

## نظرة عامة
تم إنشاء نظام إشعارات كامل لعمال النظافة باستخدام Firebase Cloud Messaging (FCM). النظام يرسل إشعارات تلقائياً عند إنشاء أي مهمة جديدة.

## المكونات

### 1. موديل الإشعارات (Notification Model)
- `app/Models/Notification.php` - موديل الإشعارات مع العلاقات والـ scopes

### 2. خدمة Firebase (Firebase Service)
- `app/Services/FirebaseNotificationService.php` - خدمة إرسال الإشعارات عبر FCM

### 3. Controller الإشعارات
- `app/Http/Controllers/API/NotificationController.php` - إدارة الإشعارات

### 4. Observers
- `app/Observers/RegularCleaningObserver.php` - إشعارات التنظيف المنتظم
- `app/Observers/DeepCleaningObserver.php` - إشعارات التنظيف العميق
- `app/Observers/MaintenanceObserver.php` - إشعارات الصيانة
- `app/Observers/PestControlObserver.php` - إشعارات مكافحة الآفات
- `app/Observers/DamageObserver.php` - إشعارات الأضرار

## الإعداد

### 1. تشغيل الـ Migrations
```bash
php artisan migrate
```

### 2. إعداد Firebase
1. نسخ ملف Firebase service account إلى `storage/app/firebase-service-account.json`
2. إضافة متغيرات البيئة إلى ملف `.env`:
```
FIREBASE_SERVER_KEY=your_firebase_server_key_here
FIREBASE_PROJECT_ID=deiyar
```

### 3. الحصول على Firebase Server Key
1. اذهب إلى [Firebase Console](https://console.firebase.google.com/)
2. اختر مشروعك
3. اذهب إلى Project Settings > Cloud Messaging
4. انسخ Server Key

## API Endpoints

### الإشعارات
- `GET /api/notifications` - عرض جميع الإشعارات
- `GET /api/notifications/stats` - إحصائيات الإشعارات
- `GET /api/notifications/{id}` - عرض إشعار محدد
- `PUT /api/notifications/{id}/read` - تحديد إشعار كمقروء
- `PUT /api/notifications/read-all` - تحديد جميع الإشعارات كمقروءة
- `DELETE /api/notifications/{id}` - حذف إشعار
- `DELETE /api/notifications` - حذف جميع الإشعارات
- `POST /api/notifications/fcm-token` - تحديث رمز FCM
- `POST /api/notifications/test` - إرسال إشعار تجريبي

### تحديث رمز FCM
```json
POST /api/notifications/fcm-token
{
    "fcm_token": "your_fcm_token_here"
}
```

## أنواع الإشعارات

### 1. التنظيف المنتظم
- **النوع**: `regular_cleaning`
- **الرسالة**: "تم إنشاء مهمة تنظيف منتظم للشاليه: {اسم الشاليه}"

### 2. التنظيف العميق
- **النوع**: `deep_cleaning`
- **الرسالة**: "تم إنشاء مهمة تنظيف عميق للشاليه: {اسم الشاليه}"

### 3. الصيانة
- **النوع**: `maintenance`
- **الرسالة**: "تم إنشاء مهمة صيانة للشاليه: {اسم الشاليه}"

### 4. مكافحة الآفات
- **النوع**: `pest_control`
- **الرسالة**: "تم إنشاء مهمة مكافحة آفات للشاليه: {اسم الشاليه}"

### 5. الأضرار
- **النوع**: `damage`
- **الرسالة**: "تم تسجيل بلاغ أضرار للشاليه: {اسم الشاليه}"

## البيانات المرسلة مع الإشعارات

كل إشعار يحتوي على البيانات التالية:
```json
{
    "type": "notification_type",
    "cleaning_id": 123,
    "chalet_id": 456,
    "chalet_name": "اسم الشاليه",
    "click_action": "FLUTTER_NOTIFICATION_CLICK",
    "sound": "default"
}
```

## الاستخدام في التطبيق

### 1. تحديث رمز FCM عند تسجيل الدخول
```dart
// في Flutter
await http.post(
  Uri.parse('$baseUrl/api/notifications/fcm-token'),
  headers: {'Authorization': 'Bearer $token'},
  body: {'fcm_token': fcmToken},
);
```

### 2. جلب الإشعارات
```dart
final response = await http.get(
  Uri.parse('$baseUrl/api/notifications'),
  headers: {'Authorization': 'Bearer $token'},
);
```

### 3. تحديد إشعار كمقروء
```dart
await http.put(
  Uri.parse('$baseUrl/api/notifications/$notificationId/read'),
  headers: {'Authorization': 'Bearer $token'},
);
```

## ملاحظات مهمة

1. **الأمان**: تأكد من حفظ ملف Firebase service account في مكان آمن
2. **الأداء**: الإشعارات تُرسل بشكل متزامن، يمكن تحسين الأداء باستخدام Queues
3. **التتبع**: جميع الإشعارات تُسجل في قاعدة البيانات مع حالة الإرسال
4. **التخصيص**: يمكن تخصيص رسائل الإشعارات حسب الحاجة

## استكشاف الأخطاء

### مشاكل شائعة:
1. **خطأ في FCM**: تأكد من صحة Server Key
2. **إشعارات لا تصل**: تحقق من رمز FCM للجهاز
3. **خطأ في الملف**: تأكد من وجود ملف Firebase service account

### سجلات الأخطاء:
- راجع ملفات السجلات في `storage/logs/`
- تحقق من استجابة FCM في السجلات

