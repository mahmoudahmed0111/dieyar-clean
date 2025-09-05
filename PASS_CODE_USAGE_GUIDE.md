# دليل استخدام Pass Code الجديد

## 📋 المتطلبات الجديدة

عند رفع النظافة من النوع **"بعد"** (`cleaning_time = "after"`)، يجب إضافة حقل `pass_code` في الطلب.

## 🔧 كيفية الاستخدام

### 1. عند رفع النظافة من النوع "بعد"

**الطلب المطلوب:**
```json
POST /api/cleaning/upload
{
    "cleaning_type": "regular",
    "chalet_id": 5,
    "cleaning_time": "after",
    "date": "2024-01-15",
    "cleaning_cost": 150.00,
    "pass_code": "NEW123CODE",  // ⭐ مطلوب في حالة after
    "inventory_items": [
        {
            "inventory_id": 1,
            "quantity": 2
        }
    ],
    "images": [...],
    "videos": [...]
}
```

**الاستجابة:**
```json
{
    "status": 201,
    "message": "تم رفع الصور والفيديوهات بعد النظافة بنجاح",
    "data": {
        "chalet_info": {
            "chalet_id": 5,
            "chalet_name": "شاليه رقم 1",
            "new_pass_code": "NEW123CODE"  // ⭐ الكود الجديد
        }
    }
}
```

### 2. عند رفع النظافة من النوع "قبل"

**الطلب المطلوب:**
```json
POST /api/cleaning/upload
{
    "cleaning_type": "regular",
    "chalet_id": 5,
    "cleaning_time": "before",
    "date": "2024-01-15",
    "images": [...],
    "videos": [...]
}
// لا حاجة لـ pass_code في هذه الحالة
```

## ✅ قواعد Pass Code

| الخاصية | القيمة |
|---------|--------|
| **مطلوب** | عند `cleaning_time = "after"` |
| **اختياري** | عند `cleaning_time = "before"` |
| **الحد الأدنى** | 4 أحرف |
| **الحد الأقصى** | 20 حرف |
| **النوع** | نص (string) |

## ❌ رسائل الخطأ المحتملة

| الخطأ | السبب |
|-------|--------|
| `كود المرور الجديد مطلوب في حالة after` | لم يتم إرسال `pass_code` مع `cleaning_time = "after"` |
| `كود المرور يجب أن يكون نص` | تم إرسال قيمة غير نصية |
| `كود المرور يجب أن يكون 4 أحرف على الأقل` | الكود قصير جداً |
| `كود المرور يجب أن يكون 20 حرف على الأكثر` | الكود طويل جداً |

## 💡 نصائح للاستخدام

1. **اختر كود آمن**: استخدم مزيج من الأحرف والأرقام
2. **تجنب الكلمات الشائعة**: مثل "1234" أو "password"
3. **استخدم طول مناسب**: 8-12 حرف مثالي
4. **تأكد من التذكر**: ستحتاج الكود لاحقاً

## 🔄 مثال عملي

```bash
# رفع النظافة من النوع "بعد" مع pass_code جديد
curl -X POST /api/cleaning/upload \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{
    "cleaning_type": "regular",
    "chalet_id": 5,
    "cleaning_time": "after",
    "date": "2024-01-15",
    "cleaning_cost": 150.00,
    "pass_code": "CHALET2024",
    "inventory_items": [
      {"inventory_id": 1, "quantity": 2}
    ],
    "images": [],
    "videos": []
  }'
```

**النتيجة:**
```json
{
    "status": 201,
    "message": "تم رفع الصور والفيديوهات بعد النظافة بنجاح",
    "data": {
        "chalet_info": {
            "chalet_id": 5,
            "chalet_name": "شاليه رقم 1",
            "new_pass_code": "CHALET2024"
        }
    }
}
```

## 📝 ملاحظات مهمة

- ✅ يتم تحديث `pass_code` فقط عند `cleaning_time = "after"`
- ✅ يمكنك اختيار أي كود تريده (ضمن الحدود)
- ✅ يتم إرجاع الكود في الاستجابة للتأكيد
- ❌ لا يتم تحديث `pass_code` عند `cleaning_time = "before"`
- ❌ لا يمكن ترك الحقل فارغاً في حالة "بعد"
