# API Documentation - Chalets

## Base URL
```
http://your-domain.com/api
```

## Authentication
- **Login**: `POST /api/login` (للحصول على token)
- **Protected Routes**: تتطلب Bearer Token في Header

## Chalets API Endpoints

### 1. جلب قائمة الشاليهات (مع البحث والفلترة)
```
GET /api/chalets
```

#### Parameters (Query String):
- `search` (optional): البحث في (الاسم، الكود، الدور، المبنى، الموقع، الوصف)
- `status` (optional): فلترة حسب الحالة (available, unavailable)
- `type` (optional): فلترة حسب النوع (apartment, studio, villa)
- `is_cleaned` (optional): فلترة حسب حالة التنظيف (true, false)
- `is_booked` (optional): فلترة حسب حالة الحجز (true, false)
- `per_page` (optional): عدد العناصر في الصفحة (default: 15)

#### مثال للاستخدام:
```
GET /api/chalets?search=شاطئ&status=available&type=villa&per_page=10
```

#### Response:
```json
{
    "data": {
        "chalets": [
            {
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
                "is_booked": false,
                "image": "http://your-domain.com/storage/chalets/images/image1.jpg",
                "images_count": 3,
                "created_at": "2025-08-14 22:15:18",
                "updated_at": "2025-08-14 22:15:18"
            }
        ],
        "pagination": {
            "current_page": 1,
            "last_page": 5,
            "per_page": 15,
            "total": 75,
            "from": 1,
            "to": 15
        }
    },
    "message": "تم جلب الشاليهات بنجاح",
    "status": 200
}
```

### 2. جلب بيانات شاليه محدد
```
GET /api/chalets/{id}
```

#### مثال للاستخدام:
```
GET /api/chalets/1
```

#### Response:
```json
{
    "data": {
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
        "is_booked": false,
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
        ],
        "images_count": 3,
        "videos_count": 1,
        "created_at": "2025-08-14 22:15:18",
        "updated_at": "2025-08-14 22:15:18"
    },
    "message": "تم جلب بيانات الشاليه بنجاح",
    "status": 200
}
```

### 3. إحصائيات الشاليهات
```
GET /api/chalets/stats
```

#### Response:
```json
{
    "data": {
        "total_chalets": 8,
        "available_chalets": 6,
        "unavailable_chalets": 2,
        "cleaned_chalets": 7,
        "not_cleaned_chalets": 1,
        "booked_chalets": 2,
        "available_for_booking": 6,
        "by_type": {
            "apartment": 3,
            "studio": 2,
            "villa": 3
        }
    },
    "message": "تم جلب إحصائيات الشاليهات بنجاح",
    "status": 200
}
```

## أمثلة للاستخدام

### البحث عن شاليهات في الغردقة:
```
GET /api/chalets?search=الغردقة
```

### البحث عن فيلات متاحة للحجز:
```
GET /api/chalets?type=villa&is_booked=false&status=available
```

### البحث عن شاليهات نظيفة:
```
GET /api/chalets?is_cleaned=true
```

### البحث في الدور الأرضي:
```
GET /api/chalets?search=الأرضي
```

### البحث في مبنى معين:
```
GET /api/chalets?search=مبنى أ
```

## Error Responses

### 404 - شاليه غير موجود:
```json
{
    "data": null,
    "message": "الشاليه غير موجود",
    "status": 404
}
```

### 422 - خطأ في البيانات:
```json
{
    "data": null,
    "message": "خطأ في البيانات المرسلة",
    "status": 422
}
```

## ملاحظات مهمة:
1. جميع الـ endpoints متاحة للضيوف (بدون مصادقة)
2. البحث يتم في الحقول التالية: name, code, floor, building, location, description
3. الصور والفيديوهات يتم إرجاعها كـ URLs كاملة
4. التواريخ يتم إرجاعها بصيغة `Y-m-d H:i:s`
5. Boolean values يتم إرجاعها كـ true/false
6. Pagination متاح لجميع النتائج
