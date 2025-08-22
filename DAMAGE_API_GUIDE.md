# دليل API الأضرار

## نظرة عامة
API شامل لإدارة تقارير الأضرار في الشاليهات مع دعم رفع الصور والفيديوهات.

## النقاط النهائية (Endpoints)

### 1. رفع تقرير ضرر جديد
**POST** `/api/damages/report`

#### المعاملات المطلوبة:
- **chalet_id**: معرف الشاليه
- **description**: وصف الضرر (حتى 1000 حرف)
- **price**: سعر الضرر (رقم)
- **images**: الصور (اختياري)
- **videos**: الفيديوهات (اختياري)

#### مثال JSON للطلب:
```json
{
    "chalet_id": 1,
    "description": "كسر في النافذة الرئيسية",
    "price": 150.00
}
```

#### كيفية الإرسال في Postman:
```
chalet_id: 1
description: كسر في النافذة الرئيسية
price: 150.00
images[]: [ملف صورة]
videos[]: [ملف فيديو]
```

### 2. جلب سجل الأضرار
**GET** `/api/damages/history`

#### المعاملات الاختيارية:
- **chalet_id**: معرف الشاليه
- **status**: حالة الضرر (`pending` أو `fixed`)
- **date_from**: تاريخ البداية
- **date_to**: تاريخ النهاية
- **per_page**: عدد العناصر في الصفحة (افتراضي: 15)

### 3. جلب تفاصيل ضرر محدد
**GET** `/api/damages/details/{id}`

### 4. تحديث حالة الضرر
**PUT** `/api/damages/{id}/status`

#### المعاملات المطلوبة:
- **status**: حالة الضرر (`pending` أو `fixed`)

## الاستجابة

### نجاح رفع التقرير (201):
```json
{
    "data": {
        "damage_record": {
            "id": 1,
            "chalet_id": 1,
            "cleaner_id": 1,
            "description": "كسر في النافذة الرئيسية",
            "price": 150.00,
            "reported_at": "2025-08-22T12:00:00.000000Z",
            "status": "pending",
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
                    "image": "http://127.0.0.1:8000/storage/damages/images/abc123.jpg"
                }
            ],
            "videos": [
                {
                    "id": 1,
                    "video": "http://127.0.0.1:8000/storage/damages/videos/def456.mp4"
                }
            ],
            "images_count": 1,
            "videos_count": 1
        }
    },
    "message": "تم رفع تقرير الضرر بنجاح",
    "status": 201
}
```

### نجاح جلب السجل (200):
```json
{
    "data": {
        "history": [
            {
                "id": 1,
                "chalet_id": 1,
                "cleaner_id": 1,
                "description": "كسر في النافذة الرئيسية",
                "price": "150.00",
                "reported_at": "2025-08-22T12:00:00.000000Z",
                "status": "pending",
                "created_at": "2025-08-22T12:00:00.000000Z",
                "updated_at": "2025-08-22T12:00:00.000000Z",
                "chalet": {
                    "id": 1,
                    "name": "شاليه تجريبي",
                    "code": "CH001",
                    "pass_code": "1234"
                },
                "images_count": 2,
                "videos_count": 1
            }
        ],
        "pagination": {
            "current_page": 1,
            "last_page": 1,
            "per_page": 15,
            "total": 1,
            "from": 1,
            "to": 1
        }
    },
    "message": "تم جلب سجل الأضرار بنجاح",
    "status": 200
}
```

### نجاح تحديث الحالة (200):
```json
{
    "data": {
        "damage_record": {
            "id": 1,
            "status": "fixed",
            "updated_at": "2025-08-22T12:30:00.000000Z"
        }
    },
    "message": "تم تحديث حالة الضرر بنجاح",
    "status": 200
}
```

## أمثلة Postman كاملة

### 1. رفع تقرير ضرر:
```
POST {{baseURL}}/api/damages/report
Authorization: Bearer {{token}}
Content-Type: multipart/form-data

chalet_id: 1
description: كسر في النافذة الرئيسية
price: 150.00
images[]: [ملف صورة]
videos[]: [ملف فيديو]
```

### 2. جلب سجل الأضرار:
```
GET {{baseURL}}/api/damages/history?status=pending&per_page=10
Authorization: Bearer {{token}}
```

### 3. جلب تفاصيل ضرر:
```
GET {{baseURL}}/api/damages/details/1
Authorization: Bearer {{token}}
```

### 4. تحديث حالة الضرر:
```
PUT {{baseURL}}/api/damages/1/status
Authorization: Bearer {{token}}
Content-Type: application/json

{
    "status": "fixed"
}
```

## ملاحظات مهمة

1. **الملفات**: الصور حتى 10MB، الفيديوهات حتى 100MB
2. **المصادقة**: مطلوب Bearer Token
3. **الشاليه**: يجب أن يكون موجود في قاعدة البيانات
4. **الحالات**: `pending` (معلق) أو `fixed` (مصلح)
5. **التواريخ**: تنسيق ISO 8601

## أنواع الملفات المدعومة

### الصور:
- JPEG, PNG, JPG, GIF, WebP

### الفيديوهات:
- MP4, AVI, MOV, WMV, WebM

## استكشاف الأخطاء

### أخطاء شائعة:
- **422**: بيانات غير صحيحة
- **404**: الشاليه أو الضرر غير موجود
- **500**: خطأ في الخادم

### نصائح:
- تأكد من صحة التوكن
- تحقق من حجم الملفات
- تأكد من وجود الشاليه
- استخدم التنسيق الصحيح للبيانات
