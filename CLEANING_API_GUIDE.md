# دليل API النظافة الشاملة

## نظرة عامة
API شامل لإدارة عمليات النظافة (العادية والعميقة) مع دعم رفع الصور والفيديوهات والمنتجات المستخدمة.

## النقاط النهائية (Endpoints)

### 1. رفع النظافة
**POST** `/api/cleaning/upload`

#### المعاملات المطلوبة:
- **cleaning_type**: نوع النظافة (`deep` أو `regular`)
- **chalet_id**: معرف الشاليه
- **cleaning_time**: وقت النظافة (`before` أو `after`)
- **date**: تاريخ النظافة
- **cleaning_cost**: سعر النظافة (مطلوب فقط في حالة `after`)
- **inventory_items**: المنتجات المستخدمة (مطلوب فقط في حالة `after`)
- **images**: الصور (اختياري)
- **videos**: الفيديوهات (اختياري)

#### مثال JSON للطلب:
```json
{
    "cleaning_type": "deep",
    "chalet_id": 1,
    "cleaning_time": "after",
    "date": "2024-01-15",
    "cleaning_cost": 150.00,
    "inventory_items": [
        {
            "inventory_id": 1,
            "quantity": 2
        },
        {
            "inventory_id": 3,
            "quantity": 1
        }
    ]
}
```

#### كيفية الإرسال في Postman:

**للصور والفيديوهات:**
- استخدم `form-data`
- أضف `images[]` و `videos[]` كـ `File` type
- أضف باقي البيانات كـ `Text` type

**لـ inventory_items في Postman:**
- أضف `inventory_items` كـ `Text` type
- أرسل البيانات كـ JSON string:
```
[{"inventory_id":1,"quantity":2},{"inventory_id":3,"quantity":1}]
```

**أو استخدم الطريقة البديلة:**
- أضف `inventory_items[0][inventory_id]` = `1`
- أضف `inventory_items[0][quantity]` = `2`
- أضف `inventory_items[1][inventory_id]` = `3`
- أضف `inventory_items[1][quantity]` = `1`

#### مثال Postman:
```
cleaning_type: deep
chalet_id: 1
cleaning_time: after
date: 2025-08-20
cleaning_cost: 150.00
inventory_items: [{"inventory_id":1,"quantity":2}]
images[]: [ملف صورة]
videos[]: [ملف فيديو]
```

### 2. جلب سجل النظافة
**GET** `/api/cleaning/history`

#### المعاملات الاختيارية:
- **cleaning_type**: نوع النظافة (`deep` أو `regular`)
- **chalet_id**: معرف الشاليه
- **date_from**: تاريخ البداية
- **date_to**: تاريخ النهاية
- **per_page**: عدد العناصر في الصفحة (افتراضي: 15)

### 3. جلب تفاصيل النظافة
**GET** `/api/cleaning/details/{id}`

#### المعاملات المطلوبة:
- **cleaning_type**: نوع النظافة (`deep` أو `regular`)

## الاستجابة

### نجاح الرفع (201):
```json
{
    "data": {
        "cleaning_record": {
            "id": 1,
            "chalet_id": 1,
            "cleaner_id": 1,
            "date": "2024-01-15",
            "cleaning_cost": 150.00,
            "status": "completed"
        },
        "uploaded_media": {
            "images": [...],
            "videos": [...],
            "images_count": 2,
            "videos_count": 1
        },
        "inventory_used": {
            "items": [...],
            "total_cost": 45.00,
            "items_count": 2
        }
    },
    "message": "تم رفع الصور والفيديوهات بعد النظافة بنجاح",
    "status": 201
}
```

### خطأ في التحقق (422):
```json
{
    "data": null,
    "message": "المنتجات يجب أن تكون مصفوفة",
    "status": 422
}
```

## ملاحظات مهمة

1. **الملفات**: الصور حتى 10MB، الفيديوهات حتى 100MB
2. **التواريخ**: تنسيق YYYY-MM-DD
3. **المصادقة**: مطلوب Bearer Token
4. **المنتجات**: يجب أن تكون موجودة في جدول inventory
5. **inventory_items**: يمكن إرسالها كـ JSON string في Postman

## أمثلة Postman كاملة

### 1. تسجيل دخول المنظف:
```
POST {{baseURL}}/api/login
Content-Type: application/json

{
    "email": "cleaner@example.com",
    "password": "password"
}
```

### 2. رفع النظافة (قبل):
```
POST {{baseURL}}/api/cleaning/upload
Authorization: Bearer {{token}}
Content-Type: multipart/form-data

cleaning_type: deep
chalet_id: 1
cleaning_time: before
date: 2025-08-20
images[]: [ملف صورة]
videos[]: [ملف فيديو]
```

### 3. رفع النظافة (بعد):
```
POST {{baseURL}}/api/cleaning/upload
Authorization: Bearer {{token}}
Content-Type: multipart/form-data

cleaning_type: deep
chalet_id: 1
cleaning_time: after
date: 2025-08-20
cleaning_cost: 150.00
inventory_items: [{"inventory_id":1,"quantity":2}]
images[]: [ملف صورة]
videos[]: [ملف فيديو]
```
