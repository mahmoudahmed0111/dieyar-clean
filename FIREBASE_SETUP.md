# إعداد Firebase للإشعارات

## المتطلبات

1. حساب Firebase
2. مشروع Firebase
3. مفتاح خادم Firebase (Server Key)

## خطوات الإعداد

### 1. إنشاء مشروع Firebase

1. اذهب إلى [Firebase Console](https://console.firebase.google.com/)
2. أنشئ مشروع جديد أو اختر مشروع موجود
3. اذهب إلى إعدادات المشروع (Project Settings)

### 2. الحصول على مفتاح الخادم

1. في إعدادات المشروع، اذهب إلى تبويب "Cloud Messaging"
2. انسخ "Server key" (مفتاح الخادم)

### 3. إعداد ملف .env

أضف المتغيرات التالية إلى ملف `.env`:

```env
FIREBASE_SERVER_KEY=your_server_key_here
FIREBASE_PROJECT_ID=your_project_id_here
```

### 4. إعداد ملف Service Account (اختياري)

إذا كنت تريد استخدام Firebase Admin SDK:

1. في إعدادات المشروع، اذهب إلى تبويب "Service accounts"
2. انقر على "Generate new private key"
3. احفظ الملف باسم `firebase-service-account.json`
4. ضع الملف في مجلد `storage/app/`

### 5. اختبار الإعداد

بعد إكمال الإعداد، يمكنك اختبار الإشعارات من خلال:

```php
$firebaseService = new \App\Services\FirebaseNotificationService();
$firebaseService->sendToAllCleaners('عنوان الإشعار', 'محتوى الإشعار');
```

## ملاحظات مهمة

- تأكد من أن مفتاح الخادم صحيح
- تأكد من أن مشروع Firebase مفعل
- تأكد من أن Cloud Messaging مفعل في المشروع
- في بيئة الإنتاج، تأكد من إضافة متغيرات البيئة الصحيحة

## استكشاف الأخطاء

إذا لم تعمل الإشعارات:

1. تحقق من سجلات Laravel (`storage/logs/laravel.log`)
2. تأكد من صحة مفتاح الخادم
3. تأكد من أن FCM tokens صحيحة في قاعدة البيانات
4. تحقق من إعدادات Firebase في Console
