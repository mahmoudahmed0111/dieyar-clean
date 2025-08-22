# أمثلة Postman لـ API النظافة

## إعداد البيئة (Environment)

### المتغيرات المطلوبة:
```
baseURL: http://127.0.0.1:8000
token: (سيتم تعيينه بعد تسجيل الدخول)
```

## 1. تسجيل دخول المنظف

### الطلب:
```
Method: POST
URL: {{baseURL}}/api/login
Headers:
  Content-Type: application/json
  Accept: application/json

Body (raw JSON):
{
    "email": "cleaner@example.com",
    "password": "password"
}
```

### الاستجابة المتوقعة:
```json
{
    "data": {
        "user": {
            "id": 1,
            "name": "منظف تجريبي",
            "email": "cleaner@example.com"
        },
        "token": "1|abc123..."
    },
    "message": "تم تسجيل الدخول بنجاح",
    "status": 200
}
```

### بعد الاستجابة:
- انسخ قيمة `token` من الاستجابة
- عيّنها في متغير البيئة `{{token}}`

## 2. رفع النظافة (قبل)

### الطلب:
```
Method: POST
URL: {{baseURL}}/api/cleaning/upload
Headers:
  Authorization: Bearer {{token}}
  Accept: application/json

Body (form-data):
cleaning_type: deep
chalet_id: 1
cleaning_time: before
date: 2025-08-20
images[]: [اختر ملف صورة]
videos[]: [اختر ملف فيديو]
```

### ملاحظات:
- تأكد من إزالة الحقول غير المستخدمة (`start_time`, `end_time`, `notes`)
- استخدم `File` type للصور والفيديوهات
- استخدم `Text` type للبيانات الأخرى

## 3. رفع النظافة (بعد)

### الطلب:
```
Method: POST
URL: {{baseURL}}/api/cleaning/upload
Headers:
  Authorization: Bearer {{token}}
  Accept: application/json

Body (form-data):
cleaning_type: deep
chalet_id: 1
cleaning_time: after
date: 2025-08-20
cleaning_cost: 150.00
inventory_items: [{"inventory_id":1,"quantity":2}]
images[]: [اختر ملف صورة]
videos[]: [اختر ملف فيديو]
```

### ملاحظات مهمة لـ inventory_items:

#### الطريقة الأولى (JSON String):
```
Key: inventory_items
Type: Text
Value: [{"inventory_id":1,"quantity":2}]
```

#### الطريقة الثانية (Array Format):
```
Key: inventory_items[0][inventory_id]
Type: Text
Value: 1

Key: inventory_items[0][quantity]
Type: Text
Value: 2

Key: inventory_items[1][inventory_id]
Type: Text
Value: 3

Key: inventory_items[1][quantity]
Type: Text
Value: 1
```

## 4. جلب سجل النظافة

### الطلب:
```
Method: GET
URL: {{baseURL}}/api/cleaning/history
Headers:
  Authorization: Bearer {{token}}
  Accept: application/json

Query Parameters (اختياري):
cleaning_type: deep
chalet_id: 1
date_from: 2025-08-01
date_to: 2025-08-31
per_page: 15
```

## 5. جلب تفاصيل النظافة

### الطلب:
```
Method: GET
URL: {{baseURL}}/api/cleaning/details/1?cleaning_type=deep
Headers:
  Authorization: Bearer {{token}}
  Accept: application/json
```

## أمثلة الاستجابات

### نجاح رفع النظافة (201):
```json
{
    "data": {
        "cleaning_record": {
            "id": 1,
            "chalet_id": 1,
            "cleaner_id": 1,
            "date": "2025-08-20",
            "cleaning_cost": 150.00,
            "status": "completed"
        },
        "uploaded_media": {
            "images": [
                {
                    "id": 1,
                    "image": "http://127.0.0.1:8000/storage/deep-cleanings/images/abc123.jpg",
                    "type": "after"
                }
            ],
            "videos": [
                {
                    "id": 1,
                    "video": "http://127.0.0.1:8000/storage/deep-cleanings/videos/def456.mp4",
                    "type": "after"
                }
            ],
            "images_count": 1,
            "videos_count": 1
        },
        "inventory_used": {
            "items": [
                {
                    "id": 1,
                    "name": "منظف أرضيات",
                    "quantity_used": 2,
                    "price": 25.00,
                    "total_cost": 50.00
                }
            ],
            "total_cost": 50.00,
            "items_count": 1
        },
        "cleaning_info": {
            "type": "نظافة عميقة",
            "time": "بعد النظافة"
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

## نصائح للاختبار

1. **تأكد من وجود بيانات تجريبية:**
   - شاليه برقم 1
   - منتج في المخزون برقم 1
   - حساب منظف صالح

2. **اختبار الملفات:**
   - استخدم صور بامتدادات: jpg, png, gif, webp
   - استخدم فيديوهات بامتدادات: mp4, avi, mov, wmv, webm
   - تأكد من أن حجم الملفات ضمن الحدود المسموحة

3. **ترتيب الاختبار:**
   1. تسجيل دخول المنظف
   2. رفع النظافة (قبل)
   3. رفع النظافة (بعد)
   4. جلب السجل
   5. جلب التفاصيل

4. **استكشاف الأخطاء:**
   - تحقق من صحة التوكن
   - تأكد من وجود الشاليه والمنتج
   - تحقق من تنسيق التواريخ
   - تأكد من صحة JSON في inventory_items
