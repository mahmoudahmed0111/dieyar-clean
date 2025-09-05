# نظام إشعارات Firebase للمنظفين - DieyarClean

## 🎯 نظرة عامة

تم إنشاء نظام إشعارات Firebase شامل لإرسال إشعارات فورية لجميع عمال النظافة عند رفع أي تقرير أو مهمة جديدة في تطبيق DieyarClean.

## ✅ ما تم إنجازه

### 1. قاعدة البيانات
- ✅ جدول `cleaners` مع عمود `fcm_token`
- ✅ جدول `notifications` لتخزين الإشعارات
- ✅ علاقات بين النماذج

### 2. ملفات Firebase
- ✅ `storage/app/firebase-service-account.json` - ملف خدمة Firebase
- ✅ إعدادات Firebase في `config/services.php`

### 3. الخدمات
- ✅ `FirebaseNotificationService` - خدمة إرسال الإشعارات
- ✅ `Notification` Model - نموذج الإشعارات

### 4. الكونترولرز المحدثة
- ✅ `CleaningController` - إشعارات عند رفع النظافة
- ✅ `DamageController` - إشعارات عند رفع التلفيات
- ✅ `MaintenanceController` - إشعارات عند رفع الصيانة
- ✅ `PestControlController` - إشعارات عند رفع المكافحة
- ✅ `AuthController` - إدارة FCM tokens

### 5. APIs الجديدة
- ✅ `POST /api/fcm-token` - تحديث FCM token
- ✅ `DELETE /api/fcm-token` - إزالة FCM token
- ✅ `POST /api/test/notification` - إرسال إشعار تجريبي
- ✅ `GET /api/test/firebase-config` - فحص إعدادات Firebase

## 🚀 كيفية العمل

### عند رفع أي تقرير:
1. **CleaningController** - رفع النظافة (قبل/بعد)
2. **DamageController** - رفع تقرير تلفيات
3. **MaintenanceController** - رفع تقرير صيانة
4. **PestControlController** - رفع تقرير مكافحة

### العملية:
1. يتم حفظ البيانات في قاعدة البيانات
2. يتم إرسال إشعار لجميع عمال النظافة الآخرين
3. يتم حفظ الإشعار في جدول `notifications`
4. يتم إرسال الإشعار عبر Firebase FCM

## 📱 APIs المتاحة

### 1. إدارة FCM Token

#### تحديث FCM Token
```http
POST /api/fcm-token
Authorization: Bearer {token}
Content-Type: application/json

{
    "fcm_token": "your_fcm_token_here"
}
```

#### إزالة FCM Token
```http
DELETE /api/fcm-token
Authorization: Bearer {token}
```

### 2. إدارة الإشعارات

#### جلب الإشعارات
```http
GET /api/notifications
Authorization: Bearer {token}
```

#### جلب إحصائيات الإشعارات
```http
GET /api/notifications/stats
Authorization: Bearer {token}
```

#### تحديد إشعار كمقروء
```http
PUT /api/notifications/{id}/read
Authorization: Bearer {token}
```

#### تحديد جميع الإشعارات كمقروءة
```http
PUT /api/notifications/read-all
Authorization: Bearer {token}
```

#### حذف إشعار
```http
DELETE /api/notifications/{id}
Authorization: Bearer {token}
```

#### حذف جميع الإشعارات
```http
DELETE /api/notifications
Authorization: Bearer {token}
```

### 3. اختبار الإشعارات

#### إرسال إشعار تجريبي
```http
POST /api/test/notification
Authorization: Bearer {token}
Content-Type: application/json

{
    "title": "إشعار تجريبي",
    "body": "هذا إشعار تجريبي لاختبار النظام",
    "cleaner_id": 1
}
```

#### فحص إعدادات Firebase
```http
GET /api/test/firebase-config
Authorization: Bearer {token}
```

## 🔔 أنواع الإشعارات

### 1. تنظيف منتظم
- **العنوان**: تنظيف منتظم جديد
- **المحتوى**: قام {اسم المنظف} برفع الصور والفيديوهات قبل/بعد النظافة للشاليه: {اسم الشاليه}

### 2. تنظيف عميق
- **العنوان**: تنظيف عميق جديد
- **المحتوى**: قام {اسم المنظف} برفع الصور والفيديوهات قبل/بعد النظافة للشاليه: {اسم الشاليه}

### 3. تقرير تلفيات
- **العنوان**: بلاغ ضرر جديد
- **المحتوى**: قام {اسم المنظف} بتسجيل بلاغ ضرر للشاليه: {اسم الشاليه}

### 4. تقرير صيانة
- **العنوان**: تقرير صيانة جديد
- **المحتوى**: قام {اسم المنظف} برفع الصور والفيديوهات قبل/بعد الصيانة للشاليه: {اسم الشاليه}

### 5. تقرير مكافحة
- **العنوان**: تقرير مكافحة جديد
- **المحتوى**: قام {اسم المنظف} برفع الصور والفيديوهات قبل/بعد المكافحة للشاليه: {اسم الشاليه}

## ⚙️ الإعدادات المطلوبة

### 1. متغيرات البيئة (.env)
```env
FIREBASE_PROJECT_ID=deiyar
FIREBASE_SERVER_KEY=your_server_key_here
```

### 2. ملف Firebase Service Account
يجب وضع ملف `firebase-service-account.json` في:
```
storage/app/firebase-service-account.json
```

## 📱 استخدام في Flutter

### 1. إعداد Firebase في Flutter
```dart
// في main.dart
import 'package:firebase_core/firebase_core.dart';
import 'package:firebase_messaging/firebase_messaging.dart';

void main() async {
  WidgetsFlutterBinding.ensureInitialized();
  await Firebase.initializeApp();
  runApp(MyApp());
}
```

### 2. الحصول على FCM Token
```dart
class NotificationService {
  static Future<String?> getFCMToken() async {
    try {
      String? token = await FirebaseMessaging.instance.getToken();
      return token;
    } catch (e) {
      print('Error getting FCM token: $e');
      return null;
    }
  }

  static Future<void> updateFCMToken(String token) async {
    try {
      final response = await http.post(
        Uri.parse('$baseURL/api/fcm-token'),
        headers: {
          'Authorization': 'Bearer $authToken',
          'Content-Type': 'application/json',
        },
        body: json.encode({'fcm_token': token}),
      );

      if (response.statusCode == 200) {
        print('FCM token updated successfully');
      }
    } catch (e) {
      print('Error updating FCM token: $e');
    }
  }
}
```

### 3. الاستماع للإشعارات
```dart
class NotificationHandler {
  static void initializeNotifications() {
    // الإشعارات في المقدمة
    FirebaseMessaging.onMessage.listen((RemoteMessage message) {
      print('Received notification: ${message.notification?.title}');
      // عرض الإشعار في التطبيق
      _showNotification(message);
    });

    // الإشعارات عند النقر
    FirebaseMessaging.onMessageOpenedApp.listen((RemoteMessage message) {
      print('Notification clicked: ${message.data}');
      // التنقل إلى الصفحة المناسبة
      _handleNotificationClick(message.data);
    });
  }

  static void _handleNotificationClick(Map<String, dynamic> data) {
    String type = data['type'] ?? '';
    int? id = int.tryParse(data['${type}_id'] ?? '');
    
    switch (type) {
      case 'regular_cleaning':
        // التنقل إلى صفحة تنظيف منتظم
        break;
      case 'deep_cleaning':
        // التنقل إلى صفحة تنظيف عميق
        break;
      case 'damage':
        // التنقل إلى صفحة التلفيات
        break;
      case 'maintenance':
        // التنقل إلى صفحة الصيانة
        break;
      case 'pest_control':
        // التنقل إلى صفحة المكافحة
        break;
    }
  }
}
```

## 🔧 استكشاف الأخطاء

### 1. مشاكل FCM Token
- تأكد من تحديث FCM Token عند تسجيل الدخول
- تأكد من صحة صلاحيات Firebase
- تحقق من سجلات Laravel

### 2. مشاكل الإشعارات
- تحقق من وجود FCM tokens في قاعدة البيانات
- تحقق من إعدادات Firebase
- تحقق من سجلات الأخطاء

### 3. سجلات مفيدة
```bash
# عرض سجلات Laravel
tail -f storage/logs/laravel.log

# البحث عن أخطاء Firebase
grep "Firebase" storage/logs/laravel.log
grep "FCM" storage/logs/laravel.log
```

## 📊 مراقبة الأداء

### 1. إحصائيات الإشعارات
```http
GET /api/notifications/stats
```

### 2. مراقبة قاعدة البيانات
```sql
-- عدد الإشعارات المرسلة
SELECT COUNT(*) FROM notifications WHERE sent_at IS NOT NULL;

-- عدد الإشعارات المقروءة
SELECT COUNT(*) FROM notifications WHERE read_at IS NOT NULL;

-- عمال النظافة الذين لديهم FCM tokens
SELECT COUNT(*) FROM cleaners WHERE fcm_token IS NOT NULL AND status = 'active';
```

## 📁 الملفات المضافة/المحدثة

### ملفات جديدة
- `storage/app/firebase-service-account.json` - ملف خدمة Firebase
- `app/Http/Controllers/API/TestNotificationController.php` - كونترولر اختبار الإشعارات
- `FIREBASE_NOTIFICATIONS_GUIDE.md` - دليل شامل للإشعارات
- `FIREBASE_NOTIFICATIONS_QUICK_START.md` - دليل سريع للبدء
- `FIREBASE_ENV_EXAMPLE.txt` - مثال لمتغيرات البيئة

### ملفات محدثة
- `app/Services/FirebaseNotificationService.php` - خدمة إرسال الإشعارات
- `app/Http/Controllers/API/CleaningController.php` - إضافة الإشعارات
- `app/Http/Controllers/API/DamageController.php` - إضافة الإشعارات
- `app/Http/Controllers/API/MaintenanceController.php` - إضافة الإشعارات
- `app/Http/Controllers/API/PestControlController.php` - إضافة الإشعارات
- `app/Http/Controllers/API/AuthController.php` - إدارة FCM tokens
- `routes/api.php` - إضافة مسارات جديدة

## 🎯 الميزات المستقبلية

- [ ] إشعارات مجدولة
- [ ] إشعارات مخصصة حسب المنطقة
- [ ] إشعارات صوتية
- [ ] إشعارات باللغة الإنجليزية
- [ ] إحصائيات مفصلة للإشعارات
- [ ] إشعارات للمديرين

## 📝 ملاحظات مهمة

1. **الأمان**: FCM tokens محمية ولا يتم مشاركتها
2. **الأداء**: الإشعارات ترسل في الخلفية لتجنب التأخير
3. **الموثوقية**: يتم حفظ الإشعارات في قاعدة البيانات كنسخة احتياطية
4. **المرونة**: يمكن تخصيص محتوى الإشعارات حسب النوع
5. **التتبع**: جميع الإشعارات مسجلة ومتتبعة

## 📞 الدعم

إذا واجهت أي مشاكل:
1. تحقق من سجلات Laravel
2. تحقق من إعدادات Firebase
3. تحقق من FCM Token في قاعدة البيانات
4. راجع ملف `FIREBASE_NOTIFICATIONS_GUIDE.md` للتفاصيل الكاملة

---

**تم إنشاء نظام إشعارات Firebase بنجاح! 🎉**

النظام جاهز للاستخدام ويمكن اختباره باستخدام APIs الاختبار المتاحة.
