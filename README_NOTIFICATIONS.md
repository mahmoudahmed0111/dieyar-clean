# نظام الإشعارات - Dieyar Clean

## ✅ تم إنشاء نظام إشعارات كامل لعمال النظافة

### 🎯 الميزات المنجزة:

1. **نظام إشعارات تلقائي** - يرسل إشعارات عند إنشاء أي مهمة جديدة
2. **دعم Firebase FCM** - إشعارات فورية للأجهزة المحمولة
3. **API كامل** - جميع العمليات المطلوبة للإشعارات
4. **Observers** - إرسال تلقائي عند إنشاء المهام
5. **Jobs** - معالجة الإشعارات في الخلفية
6. **اختبارات شاملة** - ضمان جودة النظام

### 📱 أنواع الإشعارات المدعومة:

- ✅ **التنظيف المنتظم** - عند إنشاء مهمة تنظيف منتظم
- ✅ **التنظيف العميق** - عند إنشاء مهمة تنظيف عميق  
- ✅ **الصيانة** - عند إنشاء مهمة صيانة
- ✅ **مكافحة الآفات** - عند إنشاء مهمة مكافحة آفات
- ✅ **الأضرار** - عند تسجيل بلاغ أضرار

### 🔧 الإعداد المطلوب:

1. **إضافة Firebase Server Key** إلى ملف `.env`:
```env
FIREBASE_SERVER_KEY=your_firebase_server_key_here
FIREBASE_PROJECT_ID=deiyar
```

2. **الحصول على Firebase Server Key**:
   - اذهب إلى [Firebase Console](https://console.firebase.google.com/)
   - اختر مشروعك
   - اذهب إلى Project Settings > Cloud Messaging
   - انسخ Server Key

### 📋 API Endpoints:

```
GET    /api/notifications          # عرض الإشعارات
GET    /api/notifications/stats    # إحصائيات الإشعارات  
GET    /api/notifications/{id}     # عرض إشعار محدد
PUT    /api/notifications/{id}/read # تحديد كمقروء
PUT    /api/notifications/read-all # تحديد جميع الإشعارات كمقروءة
DELETE /api/notifications/{id}     # حذف إشعار
DELETE /api/notifications          # حذف جميع الإشعارات
POST   /api/notifications/fcm-token # تحديث رمز FCM
POST   /api/notifications/test     # إرسال إشعار تجريبي
```

### 🚀 الاستخدام في التطبيق:

#### 1. تحديث رمز FCM عند تسجيل الدخول:
```dart
await http.post(
  Uri.parse('$baseUrl/api/notifications/fcm-token'),
  headers: {'Authorization': 'Bearer $token'},
  body: {'fcm_token': fcmToken},
);
```

#### 2. جلب الإشعارات:
```dart
final response = await http.get(
  Uri.parse('$baseUrl/api/notifications'),
  headers: {'Authorization': 'Bearer $token'},
);
```

#### 3. تحديد إشعار كمقروء:
```dart
await http.put(
  Uri.parse('$baseUrl/api/notifications/$notificationId/read'),
  headers: {'Authorization': 'Bearer $token'},
);
```

### 🎯 الإشعارات التلقائية:

النظام يرسل إشعارات تلقائياً عند:
- إنشاء مهمة تنظيف منتظم جديدة
- إنشاء مهمة تنظيف عميق جديدة  
- إنشاء مهمة صيانة جديدة
- إنشاء مهمة مكافحة آفات جديدة
- تسجيل بلاغ أضرار جديد

### 📊 البيانات المرسلة:

كل إشعار يحتوي على:
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

### 🛠️ الأوامر المتاحة:

```bash
# إرسال إشعار يدوياً
php artisan notification:send "عنوان الإشعار" "محتوى الإشعار" --type=general

# إرسال إشعار لعامل محدد
php artisan notification:send "عنوان" "محتوى" --cleaner-id=1

# تشغيل Jobs (للإشعارات في الخلفية)
php artisan queue:work
```

### ✅ النظام جاهز للاستخدام!

النظام مكتمل ويعمل تلقائياً. فقط أضف Firebase Server Key وابدأ في استخدام الإشعارات!

