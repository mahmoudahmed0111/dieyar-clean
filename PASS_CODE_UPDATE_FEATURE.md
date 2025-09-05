# ميزة تحديث Pass Code للشاليه

## الوصف
تم إضافة ميزة جديدة لتحديث `pass_code` للشاليه عند رفع الصور والفيديوهات من النوع "بعد" في عملية النظافة. يقوم عامل النظافة بإدخال الكود الجديد بنفسه.

## كيفية العمل
- عندما يقوم عامل النظافة برفع الصور والفيديوهات من النوع "بعد" (`cleaning_time = "after"`)
- يجب عليه إدخال `pass_code` جديد للشاليه
- يتم تحديث قاعدة البيانات بالكود المدخل
- يتم إرجاع الكود المدخل في الاستجابة للتأكيد

## التغييرات المطبقة

### في ملف `app/Http/Controllers/API/CleaningController.php`

#### 1. إضافة التحقق من Pass Code
```php
'pass_code' => 'required_if:cleaning_time,after|string|min:4|max:20',
```

#### 2. إضافة رسائل الخطأ
```php
'pass_code.required_if' => 'كود المرور الجديد مطلوب في حالة after',
'pass_code.string' => 'كود المرور يجب أن يكون نص',
'pass_code.min' => 'كود المرور يجب أن يكون 4 أحرف على الأقل',
'pass_code.max' => 'كود المرور يجب أن يكون 20 حرف على الأكثر',
```

#### 3. تحديث منطق تحديث Pass Code
```php
// تحديث pass_code للشاليه (فقط في حالة after)
if ($cleaningTime === 'after') {
    $chalet = Chalet::find($chaletId);
    if ($chalet) {
        // استخدام pass_code المدخل من المستخدم
        $chalet->update(['pass_code' => $request->pass_code]);
    }
}
```

#### 4. إضافة معلومات Pass Code في الاستجابة
```php
// إضافة pass_code الجديد للرد
if (isset($chalet)) {
    $response['chalet_info'] = [
        'chalet_id' => $chalet->id,
        'chalet_name' => $chalet->name,
        'new_pass_code' => $request->pass_code,
    ];
}
```

## متطلبات Pass Code
- **مطلوب**: عند رفع النظافة من النوع "بعد"
- **اختياري**: عند رفع النظافة من النوع "قبل"
- **الحد الأدنى**: 4 أحرف
- **الحد الأقصى**: 20 حرف
- **النوع**: نص (string)

## مثال على الطلب الجديد

### عند رفع النظافة من النوع "بعد":
```json
{
    "cleaning_type": "regular",
    "chalet_id": 5,
    "cleaning_time": "after",
    "date": "2024-01-15",
    "cleaning_cost": 150.00,
    "pass_code": "NEW123CODE",
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

### عند رفع النظافة من النوع "قبل":
```json
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

## مثال على الاستجابة الجديدة

### عند رفع النظافة من النوع "بعد":
```json
{
    "status": 201,
    "message": "تم رفع الصور والفيديوهات بعد النظافة بنجاح",
    "data": {
        "cleaning_record": {
            "id": 1,
            "chalet_id": 5,
            "cleaner_id": 3,
            "date": "2024-01-15",
            "cleaning_cost": 150.00,
            "status": "completed"
        },
        "uploaded_media": {
            "images": [...],
            "videos": [...],
            "images_count": 3,
            "videos_count": 1
        },
        "cleaning_info": {
            "type": "نظافة عادية",
            "time": "بعد النظافة"
        },
        "inventory_used": {
            "items": [...],
            "total_cost": 45.00,
            "items_count": 3
        },
        "chalet_info": {
            "chalet_id": 5,
            "chalet_name": "شاليه رقم 1",
            "new_pass_code": "NEW123CODE"
        }
    }
}
```

### عند رفع النظافة من النوع "قبل":
```json
{
    "status": 201,
    "message": "تم رفع الصور والفيديوهات قبل النظافة بنجاح",
    "data": {
        "cleaning_record": {...},
        "uploaded_media": {...},
        "cleaning_info": {
            "type": "نظافة عادية",
            "time": "قبل النظافة"
        }
        // لا يوجد chalet_info لأن pass_code لا يتم تحديثه
    }
}
```

## ملاحظات مهمة
1. يتم تحديث `pass_code` فقط عند رفع النظافة من النوع "بعد"
2. لا يتم تحديث `pass_code` عند رفع النظافة من النوع "قبل"
3. يجب على عامل النظافة إدخال الكود الجديد بنفسه
4. يتم التحقق من صحة الكود قبل التحديث
5. يتم إرجاع الكود المدخل في الاستجابة للتأكيد

## الأمان
- يتم التحقق من طول الكود (4-20 حرف)
- يتم التحقق من نوع البيانات
- يتم تحديث الكود فقط عند اكتمال عملية النظافة
- يمكن لعامل النظافة اختيار كود آمن ومناسب
