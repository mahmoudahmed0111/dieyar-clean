# دليل سريع لإشعارات Firebase

## 🚀 البدء السريع

### 1. إعداد FCM Token في Flutter
```dart
// عند تسجيل الدخول
String? fcmToken = await FirebaseMessaging.instance.getToken();
if (fcmToken != null) {
  await updateFCMToken(fcmToken);
}

// تحديث FCM Token
Future<void> updateFCMToken(String token) async {
  final response = await http.post(
    Uri.parse('$baseURL/api/fcm-token'),
    headers: {
      'Authorization': 'Bearer $authToken',
      'Content-Type': 'application/json',
    },
    body: json.encode({'fcm_token': token}),
  );
}
```

### 2. الاستماع للإشعارات
```dart
// في main.dart
FirebaseMessaging.onMessage.listen((RemoteMessage message) {
  print('إشعار جديد: ${message.notification?.title}');
  // عرض الإشعار في التطبيق
});

FirebaseMessaging.onMessageOpenedApp.listen((RemoteMessage message) {
  print('تم النقر على الإشعار');
  // التنقل إلى الصفحة المناسبة
});
```

## 📱 APIs الأساسية

### تحديث FCM Token
```http
POST /api/fcm-token
Authorization: Bearer {token}
Content-Type: application/json

{
    "fcm_token": "your_token_here"
}
```

### جلب الإشعارات
```http
GET /api/notifications
Authorization: Bearer {token}
```

### تحديد إشعار كمقروء
```http
PUT /api/notifications/{id}/read
Authorization: Bearer {token}
```

## 🔔 أنواع الإشعارات

| النوع | العنوان | المحتوى |
|-------|----------|---------|
| **تنظيف منتظم** | تنظيف منتظم جديد | قام {اسم المنظف} برفع الصور والفيديوهات |
| **تنظيف عميق** | تنظيف عميق جديد | قام {اسم المنظف} برفع الصور والفيديوهات |
| **تلفيات** | بلاغ ضرر جديد | قام {اسم المنظف} بتسجيل بلاغ ضرر |
| **صيانة** | تقرير صيانة جديد | قام {اسم المنظف} برفع تقرير صيانة |
| **مكافحة** | تقرير مكافحة جديد | قام {اسم المنظف} برفع تقرير مكافحة |

## 📊 البيانات المرسلة مع كل إشعار

```json
{
    "type": "نوع التقرير",
    "id": "معرف السجل",
    "chalet_id": "معرف الشاليه",
    "chalet_name": "اسم الشاليه",
    "cleaner_name": "اسم المنظف",
    "cleaning_time": "قبل/بعد (للتنظيف والصيانة)",
    "date": "التاريخ"
}
```

## ⚡ نصائح للاستخدام

### 1. تحديث FCM Token
- ✅ قم بتحديث FCM Token عند تسجيل الدخول
- ✅ قم بتحديثه عند تغيير الجهاز
- ✅ قم بإزالته عند تسجيل الخروج

### 2. التعامل مع الإشعارات
- ✅ استمع للإشعارات في `main.dart`
- ✅ اعرض الإشعارات في التطبيق
- ✅ تعامل مع النقر على الإشعارات

### 3. إدارة الإشعارات
- ✅ اجلب الإشعارات عند فتح التطبيق
- ✅ حدد الإشعارات كمقروءة
- ✅ احذف الإشعارات القديمة

## 🔧 استكشاف الأخطاء

### مشكلة: لا تصل الإشعارات
1. تحقق من FCM Token في قاعدة البيانات
2. تحقق من إعدادات Firebase
3. تحقق من سجلات Laravel

### مشكلة: FCM Token غير صحيح
1. احذف FCM Token القديم
2. احصل على FCM Token جديد
3. حدث FCM Token في قاعدة البيانات

### مشكلة: الإشعارات لا تظهر
1. تحقق من صلاحيات الإشعارات في الجهاز
2. تحقق من إعدادات التطبيق
3. تحقق من كود الاستماع للإشعارات

## 📝 مثال كامل

```dart
class NotificationManager {
  static Future<void> initialize() async {
    // الحصول على FCM Token
    String? token = await FirebaseMessaging.instance.getToken();
    
    if (token != null) {
      // تحديث FCM Token في الخادم
      await updateFCMToken(token);
      
      // الاستماع للإشعارات
      FirebaseMessaging.onMessage.listen(_handleNotification);
      FirebaseMessaging.onMessageOpenedApp.listen(_handleNotificationClick);
    }
  }

  static Future<void> updateFCMToken(String token) async {
    try {
      await http.post(
        Uri.parse('$baseURL/api/fcm-token'),
        headers: {
          'Authorization': 'Bearer $authToken',
          'Content-Type': 'application/json',
        },
        body: json.encode({'fcm_token': token}),
      );
    } catch (e) {
      print('Error updating FCM token: $e');
    }
  }

  static void _handleNotification(RemoteMessage message) {
    print('إشعار جديد: ${message.notification?.title}');
    // عرض الإشعار في التطبيق
  }

  static void _handleNotificationClick(RemoteMessage message) {
    print('تم النقر على الإشعار');
    // التنقل إلى الصفحة المناسبة
  }
}
```

## 🎯 الخطوات التالية

1. **إعداد Firebase في Flutter**
2. **تحديث FCM Token عند تسجيل الدخول**
3. **الاستماع للإشعارات**
4. **التعامل مع النقر على الإشعارات**
5. **إدارة الإشعارات في التطبيق**

## 📞 الدعم

إذا واجهت أي مشاكل:
1. تحقق من سجلات Laravel
2. تحقق من إعدادات Firebase
3. تحقق من FCM Token في قاعدة البيانات
4. راجع ملف `FIREBASE_NOTIFICATIONS_GUIDE.md` للتفاصيل الكاملة
