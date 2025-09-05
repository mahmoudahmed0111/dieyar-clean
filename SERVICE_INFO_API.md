# API معلومات الخدمات (الصيانة، المكافحة)

## النقاط النهائية (Endpoint)

```
GET /api/chalets/service-info
```

## الوصف
دالة لجلب معلومات الخدمات (الصيانة، المكافحة) للشاليه مع الصور والفيديوهات في تاريخ محدد.

## المعاملات المطلوبة (Query Parameters)

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `chalet_id` | integer | ✅ | معرف الشاليه |
| `type` | string | ✅ | نوع الخدمة (`maintenance` أو `pest_control`) |
| `date` | date | ✅ | تاريخ الخدمة (Y-m-d) |
| `media_type` | string | ✅ | نوع الوسائط (`before` أو `after`) |

## أمثلة للاستخدام

### طلب صيانة
```
GET /api/chalets/service-info?chalet_id=1&type=maintenance&date=2025-08-15&media_type=before
```

### مكافحة حشرات
```
GET /api/chalets/service-info?chalet_id=1&type=pest_control&date=2025-08-15&media_type=after
```

---

# API معلومات التلفيات

## النقاط النهائية (Endpoint)

```
GET /api/chalets/damage-info
```

## الوصف
دالة لجلب معلومات التلفيات للشاليه مع الصور والفيديوهات في تاريخ محدد.

## المعاملات المطلوبة (Query Parameters)

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `chalet_id` | integer | ✅ | معرف الشاليه |
| `date` | date | ✅ | تاريخ التلفيات (Y-m-d) |

## أمثلة للاستخدام

### تقرير تلفيات
```
GET /api/chalets/damage-info?chalet_id=1&date=2025-08-15
```

## Response Structure

```json
{
    "data": {
        "chalet": {
            "id": 1,
            "name": "شاليه الشاطئ الذهبي",
            "pass_code": "123654ms",
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
        "service_info": {
            "type": "تقرير تلفيات",
            "date": "2025-08-15",
            "has_record": true,
            "record_id": 5
        },
        "media": {
            "type": "بعد الخدمة",
            "images": [
                {
                    "id": 1,
                    "image": "http://your-domain.com/storage/damages/images/damage1.jpg",
                    "created_at": "2025-08-15 22:15:18"
                }
            ],
            "videos": [
                {
                    "id": 1,
                    "video": "http://your-domain.com/storage/damages/videos/damage1.mp4",
                    "created_at": "2025-08-15 22:15:18"
                }
            ]
        },
        "service_details": {
            "id": 5,
            "description": "تلف في التكييف",
            "status": "fixed",
            "price": "150.00",
            "notes": "تم إصلاح المشكلة",
            "created_at": "2025-08-15 22:15:18",
            "updated_at": "2025-08-15 23:30:00"
        }
    },
    "message": "تم جلب معلومات الخدمة بنجاح",
    "status": 200
}
```

## تفاصيل البيانات المُرجعة

### 1. Chalet Information
- **chalet**: معلومات الشاليه الأساسية

### 2. Service Information
- **type**: نوع الخدمة (تقرير تلفيات / طلب صيانة / مكافحة حشرات)
- **date**: تاريخ الخدمة
- **has_record**: هل يوجد سجل خدمة في هذا التاريخ
- **record_id**: معرف سجل الخدمة (إذا وجد)

### 3. Media
- **type**: نوع الوسائط (قبل الخدمة / بعد الخدمة)
- **images**: قائمة الصور المرفوعة
- **videos**: قائمة الفيديوهات المرفوعة

### 4. Service Details (إذا وجد سجل)
- **id**: معرف السجل
- **description**: وصف الخدمة
- **status**: حالة الخدمة
- **price**: سعر الخدمة (إذا كان متوفر)
- **notes**: ملاحظات إضافية
- **created_at**: تاريخ الإنشاء
- **updated_at**: تاريخ التحديث

## أنواع الخدمات المدعومة

### 1. Maintenance (الصيانة)
- **الجدول**: `maintenance`
- **الصور**: `maintenance_images`
- **الفيديوهات**: `maintenance_videos`
- **الحالات**: `pending`, `in_progress`, `done`

### 2. Pest Control (المكافحة)
- **الجدول**: `pest_controls`
- **الصور**: `pest_control_images`
- **الفيديوهات**: `pest_control_videos`
- **الحالات**: `pending`, `done`

## Error Responses

### 422 - خطأ في البيانات
```json
{
    "data": null,
    "message": "نوع الخدمة مطلوب",
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

## أمثلة إضافية

### البحث عن صيانة في تاريخ معين
```
GET /api/chalets/service-info?chalet_id=2&type=maintenance&date=2025-08-10&media_type=before
```

### البحث عن مكافحة في تاريخ آخر
```
GET /api/chalets/service-info?chalet_id=3&type=pest_control&date=2025-08-12&media_type=after
```

## ملاحظات مهمة

1. **قبل الخدمة**: يتم إرجاع الصور والفيديوهات المرفوعة عند طلب الخدمة
2. **بعد الخدمة**: يتم إرجاع الصور والفيديوهات المرفوعة بعد إنجاز الخدمة
3. **التواريخ**: يتم إرجاعها بصيغة `Y-m-d H:i:s`
4. **الصور والفيديوهات**: يتم إرجاعها كـ URLs كاملة
5. **الحالات**: تختلف حسب نوع الخدمة
6. **السعر**: متوفر فقط في بعض أنواع الخدمات

---

## Response Structure للتلفيات

```json
{
    "data": {
        "chalet": {
            "id": 1,
            "name": "شاليه الشاطئ الذهبي",
            "pass_code": "123654ms",
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
        "damage_info": {
            "type": "تقرير تلفيات",
            "date": "2025-08-15",
            "has_record": true,
            "record_id": 5
        },
        "media": {
            "images": [
                {
                    "id": 1,
                    "image": "http://your-domain.com/storage/damages/images/damage1.jpg",
                    "created_at": "2025-08-15 22:15:18"
                }
            ],
            "videos": [
                {
                    "id": 1,
                    "video": "http://your-domain.com/storage/damages/videos/damage1.mp4",
                    "created_at": "2025-08-15 22:15:18"
                }
            ]
        },
        "damage_details": {
            "id": 5,
            "description": "تلف في التكييف",
            "status": "fixed",
            "price": "150.00",
            "reported_at": "2025-08-15 22:15:18",
            "created_at": "2025-08-15 22:15:18",
            "updated_at": "2025-08-15 23:30:00"
        }
    },
    "message": "تم جلب معلومات التلفيات بنجاح",
    "status": 200
}
```

## تفاصيل البيانات المُرجعة للتلفيات

### 1. Chalet Information
- **chalet**: معلومات الشاليه الأساسية

### 2. Damage Information
- **type**: نوع التقرير (تقرير تلفيات)
- **date**: تاريخ التلفيات
- **has_record**: هل يوجد سجل تلفيات في هذا التاريخ
- **record_id**: معرف سجل التلفيات (إذا وجد)

### 3. Media
- **images**: قائمة الصور المرفوعة
- **videos**: قائمة الفيديوهات المرفوعة

### 4. Damage Details (إذا وجد سجل)
- **id**: معرف السجل
- **description**: وصف التلفيات
- **status**: حالة التلفيات
- **price**: سعر الإصلاح
- **reported_at**: تاريخ الإبلاغ
- **created_at**: تاريخ الإنشاء
- **updated_at**: تاريخ التحديث

## أنواع التلفيات المدعومة

### Damage (التلفيات)
- **الجدول**: `damages`
- **الصور**: `damage_images`
- **الفيديوهات**: `damage_videos`
- **الحالات**: `pending`, `fixed`

## أمثلة إضافية للتلفيات

### البحث عن تلفيات في تاريخ معين
```
GET /api/chalets/damage-info?chalet_id=2&date=2025-08-10
```

## ملاحظات مهمة للتلفيات

1. **التلفيات**: تقرير مرة واحدة فقط (لا يوجد قبل وبعد)
2. **التواريخ**: يتم إرجاعها بصيغة `Y-m-d H:i:s`
3. **الصور والفيديوهات**: يتم إرجاعها كـ URLs كاملة
4. **الحالات**: `pending` (قيد الانتظار) أو `fixed` (تم الإصلاح)
5. **السعر**: سعر الإصلاح المطلوب

## استخدام في Flutter

```dart
Future<Map<String, dynamic>> getServiceInfo({
  required int chaletId,
  required String type,
  required String date,
  required String mediaType,
}) async {
  final response = await http.get(
    Uri.parse('$baseURL/api/chalets/service-info').replace(
      queryParameters: {
        'chalet_id': chaletId.toString(),
        'type': type,
        'date': date,
        'media_type': mediaType,
      },
    ),
  );

  if (response.statusCode == 200) {
    return json.decode(response.body);
  } else {
    throw Exception('Failed to load service info');
  }
}
```
