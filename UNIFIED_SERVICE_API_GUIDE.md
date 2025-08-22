# دليل API الخدمات الموحد (صيانة ومكافحة)

## نظرة عامة
API موحد لإدارة تقارير الصيانة والمكافحة في الشاليهات مع دعم رفع الصور والفيديوهات من خلال نقطة نهائية واحدة.

## النقاط النهائية (Endpoints)

### 1. رفع تقرير الخدمة (صيانة أو مكافحة)
**POST** `/api/services/upload`

#### المعاملات المطلوبة:
- **chalet_id**: معرف الشاليه
- **service_type**: نوع الخدمة (`maintenance` أو `pest_control`)
- **cleaning_time**: وقت الخدمة (`before` أو `after`)
- **description**: وصف الخدمة (مطلوب فقط في حالة `before`)
- **price**: سعر الخدمة (مطلوب فقط في حالة `after`)
- **images**: الصور (اختياري)
- **videos**: الفيديوهات (اختياري)

#### مثال JSON للطلب:
```json
{
    "chalet_id": 1,
    "service_type": "maintenance",
    "cleaning_time": "before",
    "description": "مشكلة في التكييف"
}
```

#### كيفية الإرسال في Postman:
```
chalet_id: 1
service_type: maintenance
cleaning_time: before
description: مشكلة في التكييف
images[]: [ملف صورة]
videos[]: [ملف فيديو]
```

### 2. جلب سجل الخدمات
**GET** `/api/services/history`

#### المعاملات الاختيارية:
- **service_type**: نوع الخدمة (`maintenance` أو `pest_control`)
- **chalet_id**: معرف الشاليه
- **status**: حالة الخدمة (`in_progress` أو `completed`)
- **date_from**: تاريخ البداية
- **date_to**: تاريخ النهاية
- **per_page**: عدد العناصر في الصفحة (افتراضي: 15)

### 3. جلب تفاصيل خدمة محددة
**GET** `/api/services/details/{service_type}/{id}`

## الاستجابة

### نجاح رفع تقرير الصيانة (201):
```json
{
    "data": {
        "service_record": {
            "id": 1,
            "chalet_id": 1,
            "cleaner_id": 1,
            "service_type": "maintenance",
            "description": "مشكلة في التكييف",
            "status": "in_progress",
            "requested_at": "2025-08-22T12:00:00.000000Z",
            "completed_at": null,
            "created_at": "2025-08-22T12:00:00.000000Z"
        },
        "chalet": {
            "id": 1,
            "name": "شاليه تجريبي",
            "code": "CH001",
            "pass_code": "1234"
        },
        "uploaded_media": {
            "images": [
                {
                    "id": 1,
                    "image": "http://127.0.0.1:8000/storage/maintenance/images/abc123.jpg"
                }
            ],
            "videos": [
                {
                    "id": 1,
                    "video": "http://127.0.0.1:8000/storage/maintenance/videos/def456.mp4"
                }
            ],
            "images_count": 1,
            "videos_count": 1
        },
        "service_info": {
            "type": "صيانة",
            "time": "قبل الخدمة"
        }
    },
    "message": "تم رفع الصور والفيديوهات قبل الصيانة بنجاح",
    "status": 201
}
```

### نجاح رفع تقرير المكافحة (201):
```json
{
    "data": {
        "service_record": {
            "id": 1,
            "chalet_id": 1,
            "cleaner_id": 1,
            "service_type": "pest_control",
            "description": "",
            "status": "completed",
            "date": "2025-08-22",
            "created_at": "2025-08-22T12:00:00.000000Z"
        },
        "chalet": {
            "id": 1,
            "name": "شاليه تجريبي",
            "code": "CH001",
            "pass_code": "1234"
        },
        "uploaded_media": {
            "images": [
                {
                    "id": 1,
                    "image": "http://127.0.0.1:8000/storage/pest-control/images/abc123.jpg"
                }
            ],
            "videos": [
                {
                    "id": 1,
                    "video": "http://127.0.0.1:8000/storage/pest-control/videos/def456.mp4"
                }
            ],
            "images_count": 1,
            "videos_count": 1
        },
        "service_info": {
            "type": "مكافحة",
            "time": "بعد الخدمة"
        }
    },
    "message": "تم رفع الصور والفيديوهات بعد المكافحة بنجاح",
    "status": 201
}
```

## أمثلة Postman كاملة

### 1. رفع تقرير الصيانة (قبل):
```
POST {{baseURL}}/api/services/upload
Authorization: Bearer {{token}}
Content-Type: multipart/form-data

chalet_id: 1
service_type: maintenance
cleaning_time: before
description: مشكلة في التكييف
images[]: [ملف صورة]
videos[]: [ملف فيديو]
```

### 2. رفع تقرير الصيانة (بعد):
```
POST {{baseURL}}/api/services/upload
Authorization: Bearer {{token}}
Content-Type: multipart/form-data

chalet_id: 1
service_type: maintenance
cleaning_time: after
price: 150.00
images[]: [ملف صورة]
videos[]: [ملف فيديو]
```

### 3. رفع تقرير المكافحة (قبل):
```
POST {{baseURL}}/api/services/upload
Authorization: Bearer {{token}}
Content-Type: multipart/form-data

chalet_id: 1
service_type: pest_control
cleaning_time: before
description: وجود حشرات في المطبخ
images[]: [ملف صورة]
videos[]: [ملف فيديو]
```

### 4. رفع تقرير المكافحة (بعد):
```
POST {{baseURL}}/api/services/upload
Authorization: Bearer {{token}}
Content-Type: multipart/form-data

chalet_id: 1
service_type: pest_control
cleaning_time: after
price: 200.00
images[]: [ملف صورة]
videos[]: [ملف فيديو]
```

### 5. جلب سجل جميع الخدمات:
```
GET {{baseURL}}/api/services/history?per_page=10
Authorization: Bearer {{token}}
```

### 6. جلب سجل الصيانة فقط:
```
GET {{baseURL}}/api/services/history?service_type=maintenance&status=completed
Authorization: Bearer {{token}}
```

### 7. جلب سجل المكافحة فقط:
```
GET {{baseURL}}/api/services/history?service_type=pest_control&status=completed
Authorization: Bearer {{token}}
```

### 8. جلب تفاصيل صيانة محددة:
```
GET {{baseURL}}/api/services/details/maintenance/1
Authorization: Bearer {{token}}
```

### 9. جلب تفاصيل مكافحة محددة:
```
GET {{baseURL}}/api/services/details/pest_control/1
Authorization: Bearer {{token}}
```

## ملاحظات مهمة

1. **الملفات**: الصور حتى 10MB، الفيديوهات حتى 100MB
2. **المصادقة**: مطلوب Bearer Token
3. **الشاليه**: يجب أن يكون موجود في قاعدة البيانات
4. **الحالات**: `in_progress` (قيد التنفيذ) أو `completed` (مكتمل)
5. **التواريخ**: تنسيق ISO 8601

## أنواع الملفات المدعومة

### الصور:
- JPEG, PNG, JPG, GIF, WebP

### الفيديوهات:
- MP4, AVI, MOV, WMV, WebM

## منطق العمل

### الصيانة:
- **قبل الصيانة**: `description` مطلوب، `status` = `in_progress`
- **بعد الصيانة**: `price` مطلوب، `status` = `completed`

### المكافحة:
- **قبل المكافحة**: `description` مطلوب، `status` = `in_progress`
- **بعد المكافحة**: `price` مطلوب، `status` = `completed`

## استكشاف الأخطاء

### أخطاء شائعة:
- **422**: بيانات غير صحيحة
- **404**: الشاليه أو التقرير غير موجود
- **500**: خطأ في الخادم

### نصائح:
- تأكد من صحة التوكن
- تحقق من حجم الملفات
- تأكد من وجود الشاليه
- استخدم التنسيق الصحيح للبيانات
- تأكد من إرسال `description` في حالة `before`
- تأكد من إرسال `price` في حالة `after`
- تأكد من إرسال `service_type` صحيح (`maintenance` أو `pest_control`)

## مزايا API الموحد

1. **نقطة نهائية واحدة** - سهولة الاستخدام
2. **كود موحد** - تقليل التكرار
3. **استجابة موحدة** - تنسيق ثابت
4. **فلترة مرنة** - حسب نوع الخدمة
5. **صيانة أسهل** - تحديث واحد للجميع
