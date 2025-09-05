# نظام إشعارات Firebase للمنظفين

## 🔥 نظرة عامة

تم إنشاء نظام إشعارات Firebase شامل لإرسال إشعارات فورية لجميع عمال النظافة عند رفع أي تقرير أو مهمة جديدة.

## 📋 المكونات المطلوبة

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

## 🔔 أنواع الإشعارات

### 1. تنظيف منتظم
```json
{
    "title": "تنظيف منتظم جديد",
    "body": "قام أحمد برفع الصور والفيديوهات قبل النظافة للشاليه: شاليه الشاطئ الذهبي",
    "data": {
        "type": "regular_cleaning",
        "cleaning_id": 1,
        "chalet_id": 1,
        "chalet_name": "شاليه الشاطئ الذهبي",
        "cleaner_name": "أحمد",
        "cleaning_time": "before",
        "date": "2025-01-15"
    }
}
```

### 2. تنظيف عميق
```json
{
    "title": "تنظيف عميق جديد",
    "body": "قام محمد برفع الصور والفيديوهات بعد النظافة للشاليه: شاليه الشاطئ الذهبي",
    "data": {
        "type": "deep_cleaning",
        "cleaning_id": 2,
        "chalet_id": 1,
        "chalet_name": "شاليه الشاطئ الذهبي",
        "cleaner_name": "محمد",
        "cleaning_time": "after",
        "date": "2025-01-15"
    }
}
```

### 3. تقرير تلفيات
```json
{
    "title": "بلاغ ضرر جديد",
    "body": "قام سعد بتسجيل بلاغ ضرر للشاليه: شاليه الشاطئ الذهبي",
    "data": {
        "type": "damage",
        "damage_id": 1,
        "chalet_id": 1,
        "chalet_name": "شاليه الشاطئ الذهبي",
        "cleaner_name": "سعد",
        "description": "تلف في التكييف",
        "price": "150.00"
    }
}
```

### 4. تقرير صيانة
```json
{
    "title": "تقرير صيانة جديد",
    "body": "قام خالد برفع الصور والفيديوهات بعد الصيانة للشاليه: شاليه الشاطئ الذهبي",
    "data": {
        "type": "maintenance",
        "maintenance_id": 1,
        "chalet_id": 1,
        "chalet_name": "شاليه الشاطئ الذهبي",
        "cleaner_name": "خالد",
        "cleaning_time": "after",
        "description": "إصلاح التكييف"
    }
}
```

### 5. تقرير مكافحة
```json
{
    "title": "تقرير مكافحة جديد",
    "body": "قام علي برفع الصور والفيديوهات قبل المكافحة للشاليه: شاليه الشاطئ الذهبي",
    "data": {
        "type": "pest_control",
        "pest_control_id": 1,
        "chalet_id": 1,
        "chalet_name": "شاليه الشاطئ الذهبي",
        "cleaner_name": "علي",
        "cleaning_time": "before",
        "description": "مكافحة النمل"
    }
}
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

  static void _showNotification(RemoteMessage message) {
    // عرض الإشعار في التطبيق
    // يمكن استخدام flutter_local_notifications
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

### 4. جلب الإشعارات
```dart
class NotificationAPI {
  static Future<List<Notification>> getNotifications() async {
    try {
      final response = await http.get(
        Uri.parse('$baseURL/api/notifications'),
        headers: {'Authorization': 'Bearer $authToken'},
      );

      if (response.statusCode == 200) {
        final data = json.decode(response.body);
        return (data['data'] as List)
            .map((item) => Notification.fromJson(item))
            .toList();
      }
    } catch (e) {
      print('Error fetching notifications: $e');
    }
    return [];
  }

  static Future<void> markAsRead(int notificationId) async {
    try {
      await http.put(
        Uri.parse('$baseURL/api/notifications/$notificationId/read'),
        headers: {'Authorization': 'Bearer $authToken'},
      );
    } catch (e) {
      print('Error marking notification as read: $e');
    }
  }
}
```

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
