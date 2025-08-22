# نظام إدارة النظافة - DieyarClean

نظام شامل لإدارة عمليات النظافة في الشاليهات والفيلات مع دعم رفع الصور والفيديوهات والمنتجات المستخدمة.

## المميزات الرئيسية

- 🏠 إدارة الشاليهات والفيلات
- 🧹 نظافة عادية وعميقة
- 📸 رفع الصور والفيديوهات قبل وبعد النظافة
- 📦 إدارة المخزون والمنتجات المستخدمة
- 🔧 إدارة الصيانة والأضرار
- 🐜 مكافحة الآفات
- 📱 إشعارات Firebase
- 🔐 نظام مصادقة آمن
- 📊 إحصائيات شاملة

## المتطلبات

- PHP >= 8.1
- Laravel >= 10.0
- MySQL >= 5.7
- Node.js >= 16.0
- Composer
- npm

## التثبيت

### 1. استنساخ المشروع
```bash
git clone <repository-url>
cd DieyarClean
```

### 2. تثبيت التبعيات
```bash
composer install
npm install
```

### 3. إعداد البيئة
```bash
cp .env.example .env
php artisan key:generate
```

### 4. إعداد قاعدة البيانات
```bash
# تعديل إعدادات قاعدة البيانات في ملف .env
php artisan migrate:fresh --seed
```

### 5. إنشاء رابط رمزي للتخزين
```bash
php artisan storage:link
```

### 6. تشغيل الخادم
```bash
php artisan serve
npm run dev
```

## API النظافة

### Endpoints المتاحة

#### 1. رفع النظافة
```
POST /api/cleaning/upload
```

**المعايير المطلوبة:**
- `cleaning_type`: نوع النظافة (`deep` أو `regular`)
- `chalet_id`: معرف الشاليه
- `cleaning_time`: وقت النظافة (`before` أو `after`)
- `cleaning_date`: تاريخ النظافة
- `start_time`: وقت بداية النظافة (HH:mm)
- `end_time`: وقت انتهاء النظافة (HH:mm)
- `notes`: ملاحظات إضافية (اختياري)
- `cleaning_cost`: سعر النظافة (مطلوب فقط في حالة `after`)
- `inventory_items`: المنتجات المستخدمة (مطلوب فقط في حالة `after`)
- `images`: مصفوفة ملفات الصور (اختياري)
- `videos`: مصفوفة ملفات الفيديو (اختياري)

**مثال على الطلب:**
```json
{
    "cleaning_type": "regular",
    "chalet_id": 1,
    "cleaning_time": "before",
    "cleaning_date": "2024-01-15",
    "start_time": "09:00",
    "end_time": "11:30",
    "notes": "ملاحظات إضافية",
    "cleaning_cost": 150.00,
    "inventory_items": [
        {
            "inventory_id": 1,
            "quantity": 2
        }
    ],
    "images": [],
    "videos": []
}
```

#### 2. جلب سجل النظافة
```
GET /api/cleaning/history
```

**المعايير الاختيارية:**
- `cleaning_type`: نوع النظافة (`deep` أو `regular`)
- `chalet_id`: معرف الشاليه
- `date_from`: تاريخ البداية
- `date_to`: تاريخ النهاية
- `per_page`: عدد العناصر في الصفحة

#### 3. جلب تفاصيل نظافة محددة
```
GET /api/cleaning/details/{id}?cleaning_type=regular
```

### دعم Uppy للملفات الكبيرة

تم دمج مكتبة Uppy للتعامل مع الملفات الكبيرة:

- **الصور**: حتى 10MB
- **الفيديوهات**: حتى 100MB
- **ضغط الصور**: تلقائي لتحسين الأداء
- **شريط التقدم**: عرض حالة الرفع
- **معالجة الأخطاء**: رسائل واضحة

### مثال على استخدام Uppy

```javascript
import CleaningUploader from '/js/cleaning-upload.js'

const uploader = new CleaningUploader({
    token: 'your-auth-token',
    endpoint: '/api/cleaning/upload'
})

const cleaningData = {
    cleaning_type: 'regular',
    chalet_id: 1,
    cleaning_time: 'before',
    cleaning_date: '2024-01-15',
    start_time: '09:00',
    end_time: '11:30',
    notes: 'ملاحظات إضافية'
}

uploader.uploadCleaning(cleaningData)
```

## إعداد Firebase للإشعارات

### 1. إنشاء مشروع Firebase
1. اذهب إلى [Firebase Console](https://console.firebase.google.com/)
2. أنشئ مشروع جديد
3. اذهب إلى إعدادات المشروع

### 2. الحصول على مفتاح الخادم
1. في إعدادات المشروع، اذهب إلى تبويب "Cloud Messaging"
2. انسخ "Server key"

### 3. إعداد ملف .env
```env
FIREBASE_SERVER_KEY=your_server_key_here
FIREBASE_PROJECT_ID=your_project_id_here
```

### 4. إعداد ملف Service Account (اختياري)
1. في إعدادات المشروع، اذهب إلى تبويب "Service accounts"
2. انقر على "Generate new private key"
3. احفظ الملف باسم `firebase-service-account.json`
4. ضع الملف في مجلد `storage/app/`

## هيكل المشروع

```
DieyarClean/
├── app/
│   ├── Http/Controllers/API/
│   │   ├── CleaningController.php      # API النظافة الرئيسي
│   │   ├── AuthController.php          # مصادقة المنظفين
│   │   ├── ChaletController.php        # إدارة الشاليهات
│   │   └── ...
│   ├── Models/
│   │   ├── RegularCleaning.php         # نموذج النظافة العادية
│   │   ├── DeepCleaning.php            # نموذج النظافة العميقة
│   │   └── ...
│   └── Services/
│       └── FirebaseNotificationService.php
├── resources/
│   ├── js/
│   │   └── cleaning-upload.js          # JavaScript لرفع النظافة
│   └── views/
│       └── cleaning-upload-example.blade.php
├── routes/
│   ├── api.php                         # API routes
│   └── web.php                         # Web routes
└── database/
    └── migrations/                     # ملفات الهجرة
```

## الملفات المهمة

### API النظافة
- `app/Http/Controllers/API/CleaningController.php` - Controller الرئيسي لرفع النظافة
- `resources/js/cleaning-upload.js` - JavaScript لرفع الملفات مع Uppy
- `resources/views/cleaning-upload-example.blade.php` - صفحة مثال لرفع النظافة

### التوثيق
- `CLEANING_API_GUIDE.md` - دليل شامل لاستخدام API النظافة
- `FIREBASE_SETUP.md` - دليل إعداد Firebase
- `NOTIFICATION_SETUP.md` - دليل إعداد الإشعارات

## الاستخدام

### 1. تسجيل دخول المنظف
```bash
POST /api/login
{
    "email": "cleaner@example.com",
    "password": "password"
}
```

### 2. رفع النظافة
```bash
POST /api/cleaning/upload
# مع البيانات والملفات المطلوبة
```

### 3. جلب سجل النظافة
```bash
GET /api/cleaning/history
```

### 4. جلب تفاصيل نظافة محددة
```bash
GET /api/cleaning/details/1?cleaning_type=regular
```

## الأمان

- جميع API endpoints محمية بالمصادقة
- التحقق من صحة جميع البيانات المدخلة
- حماية من CSRF attacks
- تشفير كلمات المرور
- التحقق من صلاحيات المستخدم

## الأداء

- دعم الملفات الكبيرة (حتى 100MB)
- ضغط الصور تلقائياً
- تخزين مؤقت للبيانات
- تحسين استعلامات قاعدة البيانات
- معالجة متوازية للملفات

## الدعم

للمساعدة والدعم التقني:
- 📧 البريد الإلكتروني: support@dieyar.com
- 📱 الهاتف: +966-XX-XXX-XXXX
- 🌐 الموقع: https://dieyar.com

## الترخيص

هذا المشروع مرخص تحت رخصة MIT. راجع ملف `LICENSE` للتفاصيل.

## المساهمة

نرحب بالمساهمات! يرجى اتباع الخطوات التالية:

1. Fork المشروع
2. إنشاء branch جديد للميزة
3. Commit التغييرات
4. Push إلى Branch
5. إنشاء Pull Request

## التحديثات

### الإصدار 1.0.0
- ✅ API رفع النظافة الشامل
- ✅ دعم Uppy للملفات الكبيرة
- ✅ إدارة المخزون التلقائية
- ✅ إشعارات Firebase
- ✅ نظام مصادقة آمن
- ✅ واجهة مستخدم حديثة
