# Chalet Info API Documentation

## Endpoint
```
GET /api/chalets/info
```

## الوصف
دالة لجلب معلومات الشاليه مع سجل النظافة والمخزون المستخدم في تاريخ محدد.

## Parameters (Query String)

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `chalet_id` | integer | ✅ | معرف الشاليه |
| `cleaning_type` | string | ✅ | نوع النظافة (`regular` أو `deep`) |
| `cleaning_date` | date | ✅ | تاريخ النظافة (Y-m-d) |

## أمثلة للاستخدام

### نظافة عادية
```
GET /api/chalets/info?chalet_id=1&cleaning_type=regular&cleaning_date=2025-08-14
```

### نظافة عميقة
```
GET /api/chalets/info?chalet_id=1&cleaning_type=deep&cleaning_date=2025-08-14
```

## Response Structure

```json
{
    "data": {
        "chalet": {
            "id": 1,
            "name": "شاليه الشاطئ الذهبي",
            "code": "CH001",
            "floor": "الأرضي",
            "building": "مبنى أ",
            "location": "شاطئ البحر الأحمر - الغردقة",
            "description": "شاليه فاخر بإطلالة مباشرة على البحر",
            "status": "available",
            "type": "apartment",
            "is_cleaned": true,
            "is_booked": false
        },
        "cleaning_info": {
            "type": "نظافة عادية",
            "date": "2025-08-14",
            "has_record": true,
            "record_id": 5
        },
        "media": {
            "before_cleaning": {
                "images": [
                    {
                        "id": 1,
                        "image": "http://your-domain.com/storage/chalets/images/image1.jpg",
                        "created_at": "2025-08-14 22:15:18"
                    }
                ],
                "videos": [
                    {
                        "id": 1,
                        "video": "http://your-domain.com/storage/chalets/videos/video1.mp4",
                        "created_at": "2025-08-14 22:15:18"
                    }
                ]
            },
            "after_cleaning": {
                "images": [
                    {
                        "id": 10,
                        "image": "http://your-domain.com/storage/regular_cleaning_images/after1.jpg",
                        "created_at": "2025-08-14 23:30:00"
                    }
                ],
                "videos": [
                    {
                        "id": 5,
                        "video": "http://your-domain.com/storage/regular_cleaning_videos/after1.mp4",
                        "created_at": "2025-08-14 23:30:00"
                    }
                ]
            }
        },
        "inventory_used": {
            "records": [
                {
                    "id": 1,
                    "product_name": "منظف أرضيات",
                    "image": "http://your-domain.com/storage/inventory/cleaner1.jpg",
                    "price": 25.50,
                    "quantity_used": 2,
                    "cleaner_rate": 50.00,
                    "total_cost": 101.00
                },
                {
                    "id": 3,
                    "product_name": "مطهر عام",
                    "image": "http://your-domain.com/storage/inventory/disinfectant1.jpg",
                    "price": 15.75,
                    "quantity_used": 1,
                    "cleaner_rate": 0.00,
                    "total_cost": 15.75
                }
            ],
            "total_cost": 116.75,
            "items_count": 2
        }
    },
    "message": "تم جلب معلومات الشاليه بنجاح",
    "status": 200
}
```

## تفاصيل البيانات المُرجعة

### 1. Chalet Information
- **chalet**: معلومات الشاليه الأساسية

### 2. Cleaning Information
- **type**: نوع النظافة (نظافة عادية / نظافة عميقة)
- **date**: تاريخ النظافة
- **has_record**: هل يوجد سجل نظافة في هذا التاريخ
- **record_id**: معرف سجل النظافة (إذا وجد)

### 3. Media
- **before_cleaning**: الصور والفيديوهات الأصلية للشاليه
- **after_cleaning**: الصور والفيديوهات بعد النظافة (إذا وجدت)

### 4. Inventory Used
- **records**: قائمة المنتجات المستخدمة في النظافة
  - **product_name**: اسم المنتج
  - **image**: صورة المنتج
  - **price**: سعر الوحدة
  - **quantity_used**: الكمية المستخدمة
  - **cleaner_rate**: تسعيرة عامل النظافة
  - **total_cost**: التكلفة الإجمالية للمنتج (السعر × الكمية + تسعيرة العامل)
- **total_cost**: إجمالي تكلفة النظافة
- **items_count**: عدد المنتجات المستخدمة

## Error Responses

### 422 - خطأ في البيانات
```json
{
    "data": null,
    "message": "معرف الشاليه مطلوب",
    "status": 422
}
```

### 404 - شاليه غير موجود
```json
{
    "data": null,
    "message": "الشاليه غير موجود",
    "status": 404
}
```

## ملاحظات مهمة

1. **قبل النظافة**: يتم إرجاع الصور والفيديوهات الأصلية للشاليه
2. **بعد النظافة**: يتم إرجاع الصور والفيديوهات المرفوعة بعد النظافة (إذا وجدت)
3. **المخزون**: يتم حساب التكلفة الإجمالية لكل منتج (السعر × الكمية + تسعيرة العامل)
4. **التكلفة الإجمالية**: مجموع تكلفة جميع المنتجات المستخدمة
5. **التواريخ**: يتم إرجاعها بصيغة `Y-m-d H:i:s`
6. **الصور والفيديوهات**: يتم إرجاعها كـ URLs كاملة

## أمثلة إضافية

### البحث عن نظافة عميقة في تاريخ معين
```
GET /api/chalets/info?chalet_id=2&cleaning_type=deep&cleaning_date=2025-08-15
```

### البحث عن نظافة عادية في تاريخ آخر
```
GET /api/chalets/info?chalet_id=3&cleaning_type=regular&cleaning_date=2025-08-10
```
