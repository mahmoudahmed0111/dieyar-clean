# 🔧 إصلاح خطأ العمود cleaning_cost

## ❌ **المشكلة:**
```
SQLSTATE[42S22]: Column not found: 1054 Unknown column 'cleaning_cost' in 'field list'
```

## 🔍 **السبب:**
- الكود كان يحاول استخدام `cleaning_cost` 
- لكن قاعدة البيانات تحتوي على `price`

## ✅ **الحل المطبق:**

### **1. تحديث النماذج:**

#### **DeepCleaning.php:**
```php
protected $fillable = [
    'cleaner_id',
    'chalet_id', 
    'date',
    'price',        // ← تغيير من cleaning_cost
    'cleaning_type',
    'status'
];

protected $casts = [
    'date' => 'date',
    'price' => 'decimal:2',  // ← تغيير من cleaning_cost
];
```

#### **RegularCleaning.php:**
```php
protected $fillable = [
    'cleaner_id',
    'chalet_id',
    'date', 
    'price',        // ← تغيير من cleaning_cost
    'status',
    'cleaning_type'
];

protected $casts = [
    'date' => 'date',
    'price' => 'decimal:2',  // ← تغيير من cleaning_cost
];
```

### **2. تحديث CleaningController:**

#### **تحديث سعر النظافة:**
```php
// قبل الإصلاح
$cleaningRecord->update(['cleaning_cost' => $request->cleaning_cost]);

// بعد الإصلاح
$cleaningRecord->update(['price' => $request->cleaning_cost]);
```

#### **إرجاع البيانات:**
```php
// قبل الإصلاح  
'cleaning_cost' => $cleaningRecord->cleaning_cost ?? 0,

// بعد الإصلاح
'cleaning_cost' => $cleaningRecord->price ?? 0,
```

#### **استعلامات قاعدة البيانات:**
```php
// قبل الإصلاح
'cleaning_cost',

// بعد الإصلاح  
'price as cleaning_cost',
```

---

## 🎯 **النتيجة:**

- ✅ الآن API يتعامل مع العمود الصحيح `price`
- ✅ يعرض البيانات للمستخدم كـ `cleaning_cost` (للتوافق مع Frontend)
- ✅ جميع دوال النظافة تعمل بشكل صحيح

---

## 🧪 **الاختبار:**

```bash
POST {{baseURL}}/api/cleaning/upload

{
  "cleaning_type": "deep",
  "chalet_id": 1,
  "cleaning_time": "after", 
  "date": "2025-08-20",
  "cleaning_cost": 45,  // ← سيتم حفظه في عمود price
  "inventory_items": [{"inventory_id":1,"quantity":2}]
}
```

**الاستجابة المتوقعة:**
```json
{
  "data": {
    "cleaning_record": {
      "id": 1,
      "cleaning_cost": 45,  // ← يُعرض من عمود price
      "status": "completed"
    }
  },
  "message": "تم رفع الصور والفيديوهات بعد النظافة بنجاح",
  "status": 201
}
```

## 🚀 **API جاهز للاستخدام الآن!**
