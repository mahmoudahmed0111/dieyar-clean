# دليل API الصيانة والمكافحة

## نظرة عامة
API شامل لإدارة تقارير الصيانة والمكافحة في الشاليهات مع دعم رفع الصور والفيديوهات.

## النقاط النهائية (Endpoints)

### 1. رفع تقرير الصيانة
**POST** `/api/maintenance/upload`

#### المعاملات المطلوبة:
- **chalet_id**: معرف الشاليه
- **cleaning_time**: وقت الصيانة (`before` أو `after`)
- **description**: وصف الصيانة (مطلوب فقط في حالة `before`)
- **price**: سعر الصيانة (مطلوب فقط في حالة `after`)
- **images**: الصور (اختياري)
- **videos**: الفيديوهات (اختياري)

#### مثال JSON للطلب:
```json
{
    "chalet_id": 1,
    "cleaning_time": "before",
    "description": "مشكلة في التكييف"
}
```

#### كيفية الإرسال في Postman:
```
chalet_id: 1
cleaning_time: before
description: مشكلة في التكييف
images[]: [ملف صورة]
videos[]: [ملف فيديو]
```

### 2. رفع تقرير المكافحة
**POST** `/api/pest-control/upload`

#### المعاملات المطلوبة:
- **chalet_id**: معرف الشاليه
- **cleaning_time**: وقت المكافحة (`before` أو `after`)
- **description**: وصف المكافحة (مطلوب فقط في حالة `before`)
- **price**: سعر المكافحة (مطلوب فقط في حالة `after`)
- **images**: الصور (اختياري)
- **videos**: الفيديوهات (اختياري)

#### مثال JSON للطلب:
```json
{
    "chalet_id": 1,
    "cleaning_time": "after",
    "price": 200.00
}
```

#### كيفية الإرسال في Postman:
```
chalet_id: 1
cleaning_time: after
price: 200.00
images[]: [ملف صورة]
videos[]: [ملف فيديو]
```

### 3. جلب سجل الصيانة
**GET** `/api/maintenance/history`

#### المعاملات الاختيارية:
- **chalet_id**: معرف الشاليه
- **status**: حالة الصيانة (`in_progress` أو `completed`)
- **date_from**: تاريخ البداية
- **date_to**: تاريخ النهاية
- **per_page**: عدد العناصر في الصفحة (افتراضي: 15)

### 4. جلب سجل المكافحة
**GET** `/api/pest-control/history`

#### المعاملات الاختيارية:
- **chalet_id**: معرف الشاليه
- **status**: حالة المكافحة (`in_progress` أو `completed`)
- **date_from**: تاريخ البداية
- **date_to**: تاريخ النهاية
- **per_page**: عدد العناصر في الصفحة (افتراضي: 15)

### 5. جلب تفاصيل الصيانة
**GET** `/api/maintenance/details/{id}`

### 6. جلب تفاصيل المكافحة
**GET** `/api/pest-control/details/{id}`

## الاستجابة

### نجاح رفع تقرير الصيانة (201):
```json
{
    "data": {
        "maintenance_record": {
            "id": 1,
            "chalet_id": 1,
            "cleaner_id": 1,
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
        "maintenance_info": {
            "time": "قبل الصيانة"
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
        "pest_control_record": {
            "id": 1,
            "chalet_id": 1,
            "cleaner_id": 1,
            "date": "2025-08-22",
            "description": "",
            "status": "completed",
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
        "pest_control_info": {
            "time": "بعد المكافحة"
        }
    },
    "message": "تم رفع الصور والفيديوهات بعد المكافحة بنجاح",
    "status": 201
}
```

## أمثلة Postman كاملة

### 1. رفع تقرير الصيانة (قبل):
```
POST {{baseURL}}/api/maintenance/upload
Authorization: Bearer {{token}}
Content-Type: multipart/form-data

chalet_id: 1
cleaning_time: before
description: مشكلة في التكييف
images[]: [ملف صورة]
videos[]: [ملف فيديو]
```

### 2. رفع تقرير الصيانة (بعد):
```
POST {{baseURL}}/api/maintenance/upload
Authorization: Bearer {{token}}
Content-Type: multipart/form-data

chalet_id: 1
cleaning_time: after
price: 150.00
images[]: [ملف صورة]
videos[]: [ملف فيديو]
```

### 3. رفع تقرير المكافحة (قبل):
```
POST {{baseURL}}/api/pest-control/upload
Authorization: Bearer {{token}}
Content-Type: multipart/form-data

chalet_id: 1
cleaning_time: before
description: وجود حشرات في المطبخ
images[]: [ملف صورة]
videos[]: [ملف فيديو]
```

### 4. رفع تقرير المكافحة (بعد):
```
POST {{baseURL}}/api/pest-control/upload
Authorization: Bearer {{token}}
Content-Type: multipart/form-data

chalet_id: 1
cleaning_time: after
price: 200.00
images[]: [ملف صورة]
videos[]: [ملف فيديو]
```

### 5. جلب سجل الصيانة:
```
GET {{baseURL}}/api/maintenance/history?status=completed&per_page=10
Authorization: Bearer {{token}}
```

### 6. جلب سجل المكافحة:
```
GET {{baseURL}}/api/pest-control/history?status=completed&per_page=10
Authorization: Bearer {{token}}
```

### 7. جلب تفاصيل الصيانة:
```
GET {{baseURL}}/api/maintenance/details/1
Authorization: Bearer {{token}}
```

### 8. جلب تفاصيل المكافحة:
```
GET {{baseURL}}/api/pest-control/details/1
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
