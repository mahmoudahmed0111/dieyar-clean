# دليل استخدام API معلومات الخدمات والتلفيات

## 🚀 النقاط النهائية الجديدة

تم إضافة API جديد لجلب معلومات الخدمات والتلفيات للشاليه:

### 1. الخدمات (الصيانة، المكافحة)
```
GET /api/chalets/service-info
```

### 2. التلفيات
```
GET /api/chalets/damage-info
```

## 📋 المعاملات المطلوبة

### للخدمات (service-info)
| المعامل | النوع | مطلوب | الوصف |
|---------|-------|--------|--------|
| `chalet_id` | integer | ✅ | معرف الشاليه |
| `type` | string | ✅ | نوع الخدمة |
| `date` | date | ✅ | تاريخ الخدمة |
| `media_type` | string | ✅ | نوع الوسائط |

### للتلفيات (damage-info)
| المعامل | النوع | مطلوب | الوصف |
|---------|-------|--------|--------|
| `chalet_id` | integer | ✅ | معرف الشاليه |
| `date` | date | ✅ | تاريخ التلفيات |

## 🔧 أنواع الخدمات المدعومة

### 1. الصيانة (Maintenance)
```bash
GET /api/chalets/service-info?chalet_id=1&type=maintenance&date=2025-08-15&media_type=before
```

### 2. المكافحة (Pest Control)
```bash
GET /api/chalets/service-info?chalet_id=1&type=pest_control&date=2025-08-15&media_type=after
```

### 3. التلفيات (Damage)
```bash
GET /api/chalets/damage-info?chalet_id=1&date=2025-08-15
```

## 📱 استخدام في Flutter

```dart
class ServiceInfoAPI {
  static const String baseURL = 'https://your-domain.com/api';
  
  // جلب معلومات الخدمات (الصيانة، المكافحة)
  static Future<Map<String, dynamic>> getServiceInfo({
    required int chaletId,
    required String type,
    required String date,
    required String mediaType,
  }) async {
    try {
      final response = await http.get(
        Uri.parse('$baseURL/chalets/service-info').replace(
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
    } catch (e) {
      throw Exception('Network error: $e');
    }
  }

  // جلب معلومات التلفيات
  static Future<Map<String, dynamic>> getDamageInfo({
    required int chaletId,
    required String date,
  }) async {
    try {
      final response = await http.get(
        Uri.parse('$baseURL/chalets/damage-info').replace(
          queryParameters: {
            'chalet_id': chaletId.toString(),
            'date': date,
          },
        ),
      );

      if (response.statusCode == 200) {
        return json.decode(response.body);
      } else {
        throw Exception('Failed to load damage info');
      }
    } catch (e) {
      throw Exception('Network error: $e');
    }
  }
}
```

## 🎯 أمثلة الاستخدام

### جلب طلب صيانة
```dart
final result = await ServiceInfoAPI.getServiceInfo(
  chaletId: 1,
  type: 'maintenance',
  date: '2025-08-15',
  mediaType: 'before',
);
```

### جلب مكافحة حشرات
```dart
final result = await ServiceInfoAPI.getServiceInfo(
  chaletId: 1,
  type: 'pest_control',
  date: '2025-08-15',
  mediaType: 'after',
);
```

### جلب تقرير تلفيات
```dart
final result = await ServiceInfoAPI.getDamageInfo(
  chaletId: 1,
  date: '2025-08-15',
);
```

## 📊 الاستجابة المتوقعة

```json
{
    "data": {
        "chalet": {
            "id": 1,
            "name": "شاليه الشاطئ الذهبي",
            "pass_code": "123654ms",
            "code": "CH001",
            "status": "available"
        },
        "service_info": {
            "type": "تقرير تلفيات",
            "date": "2025-08-15",
            "has_record": true,
            "record_id": 5
        },
        "media": {
            "type": "بعد الخدمة",
            "images": [...],
            "videos": [...]
        },
        "service_details": {
            "id": 5,
            "description": "تلف في التكييف",
            "status": "fixed",
            "price": "150.00"
        }
    },
    "message": "تم جلب معلومات الخدمة بنجاح",
    "status": 200
}
```

## ⚠️ رسائل الخطأ المحتملة

### للخدمات (service-info)
| الخطأ | السبب | الحل |
|-------|--------|------|
| `نوع الخدمة مطلوب` | لم يتم إرسال `type` | أرسل `type` مع القيمة |
| `نوع الخدمة يجب أن يكون maintenance أو pest_control` | قيمة `type` غير صحيحة | استخدم إحدى القيم المسموحة |
| `تاريخ الخدمة مطلوب` | لم يتم إرسال `date` | أرسل `date` بصيغة Y-m-d |
| `نوع الوسائط مطلوب` | لم يتم إرسال `media_type` | أرسل `media_type` (before/after) |

### للتلفيات (damage-info)
| الخطأ | السبب | الحل |
|-------|--------|------|
| `تاريخ التلفيات مطلوب` | لم يتم إرسال `date` | أرسل `date` بصيغة Y-m-d |
| `الشاليه غير موجود` | `chalet_id` غير موجود | تأكد من صحة معرف الشاليه |

## 🔍 اختبار API

### اختبار الخدمات باستخدام Postman
1. **Method**: GET
2. **URL**: `{{baseURL}}/api/chalets/service-info`
3. **Query Params**:
   - `chalet_id`: 1
   - `type`: maintenance
   - `date`: 2025-08-15
   - `media_type`: before

### اختبار التلفيات باستخدام Postman
1. **Method**: GET
2. **URL**: `{{baseURL}}/api/chalets/damage-info`
3. **Query Params**:
   - `chalet_id`: 1
   - `date`: 2025-08-15

### باستخدام cURL
```bash
# اختبار الخدمات
curl -X GET "https://your-domain.com/api/chalets/service-info?chalet_id=1&type=maintenance&date=2025-08-15&media_type=before"

# اختبار التلفيات
curl -X GET "https://your-domain.com/api/chalets/damage-info?chalet_id=1&date=2025-08-15"
```

## 📝 ملاحظات مهمة

### للخدمات (الصيانة، المكافحة)
- ✅ **قبل الخدمة**: يعرض الصور والفيديوهات عند طلب الخدمة
- ✅ **بعد الخدمة**: يعرض الصور والفيديوهات بعد إنجاز الخدمة
- ✅ **التواريخ**: بصيغة `Y-m-d`
- ✅ **الصور والفيديوهات**: URLs كاملة
- ✅ **الحالات**: تختلف حسب نوع الخدمة

### للتلفيات
- ✅ **تقرير مرة واحدة**: لا يوجد قبل وبعد
- ✅ **التواريخ**: بصيغة `Y-m-d`
- ✅ **الصور والفيديوهات**: URLs كاملة
- ✅ **الحالات**: `pending` أو `fixed`
- ✅ **السعر**: سعر الإصلاح المطلوب

## 🎨 هيكلة الكود

تم إنشاء API بنفس هيكلة باقي الـ APIs:
- ✅ نفس نمط التحقق من البيانات
- ✅ نفس نمط معالجة الأخطاء
- ✅ نفس نمط الاستجابة
- ✅ نفس نمط التوثيق
- ✅ نفس نمط التسمية
