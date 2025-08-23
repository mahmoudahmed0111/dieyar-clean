# 🧹 Cleaning API - الإصدار المحدث

## ✅ **تم إصلاح جميع الأخطاء**

### **🔧 الإصلاحات المطبقة:**

1. **إضافة عمود `cleaning_type` لجدول `deep_cleanings`**
2. **إضافة أعمدة `status` و `cleaning_type` لجدول `regular_cleanings`**
3. **تحديث نموذج `RegularCleaning` لإضافة `cleaning_type` إلى `fillable`**
4. **تحديث `CleaningController` لإضافة `cleaning_type` لجميع أنواع النظافة**

---

## 📋 **API Endpoints**

### **1. رفع النظافة**
```
POST /api/cleaning/upload
```

#### **المعاملات المطلوبة:**
- `cleaning_type` (string): `deep` أو `regular`
- `chalet_id` (integer): معرف الشاليه
- `cleaning_time` (string): `before` أو `after`
- `date` (date): تاريخ النظافة

#### **المعاملات المطلوبة في حالة `after`:**
- `cleaning_cost` (numeric): سعر النظافة
- `inventory_items` (array): المنتجات المستخدمة

#### **المعاملات الاختيارية:**
- `images[]` (files): الصور
- `videos[]` (files): الفيديوهات

---

### **2. جلب سجل النظافة**
```
GET /api/cleaning/history
```

#### **المعاملات الاختيارية:**
- `cleaning_type` (string): `deep` أو `regular`
- `chalet_id` (integer): معرف الشاليه
- `date_from` (date): تاريخ البداية
- `date_to` (date): تاريخ النهاية
- `per_page` (integer): عدد العناصر في الصفحة

---

### **3. جلب تفاصيل النظافة**
```
GET /api/cleaning/details/{id}?cleaning_type={type}
```

#### **المعاملات المطلوبة:**
- `id` (integer): معرف سجل النظافة
- `cleaning_type` (string): `deep` أو `regular`

---

## 📊 **هيكل قاعدة البيانات المحدث**

### **جدول `deep_cleanings`:**
```sql
- id (primary key)
- cleaner_id (foreign key)
- chalet_id (foreign key)
- cleaning_type (string) ✅ **جديد**
- date (date)
- status (string)
- cleaning_cost (decimal)
- notes (text)
- created_at, updated_at
```

### **جدول `regular_cleanings`:**
```sql
- id (primary key)
- cleaner_id (foreign key)
- chalet_id (foreign key)
- status (string) ✅ **جديد**
- cleaning_type (string) ✅ **جديد**
- date (date)
- cleaning_cost (decimal)
- notes (text)
- created_at, updated_at
```

---

## 🧪 **أمثلة الاستخدام**

### **مثال 1: رفع نظافة عميقة (قبل)**
```bash
curl -X POST "{{baseURL}}/api/cleaning/upload" \
  -H "Authorization: Bearer {token}" \
  -F "cleaning_type=deep" \
  -F "chalet_id=1" \
  -F "cleaning_time=before" \
  -F "date=2025-08-20" \
  -F "images[]=@image1.jpg" \
  -F "images[]=@image2.jpg" \
  -F "videos[]=@video1.mp4"
```

### **مثال 2: رفع نظافة عادية (بعد)**
```bash
curl -X POST "{{baseURL}}/api/cleaning/upload" \
  -H "Authorization: Bearer {token}" \
  -F "cleaning_type=regular" \
  -F "chalet_id=1" \
  -F "cleaning_time=after" \
  -F "date=2025-08-20" \
  -F "cleaning_cost=45.50" \
  -F "inventory_items=[{\"inventory_id\":1,\"quantity\":2}]" \
  -F "images[]=@after1.jpg" \
  -F "videos[]=@after1.mp4"
```

---

## ✅ **الاستجابة المتوقعة**

### **نجح العملية:**
```json
{
  "data": {
    "cleaning_record": {
      "id": 1,
      "chalet_id": 1,
      "cleaner_id": 1,
      "date": "2025-08-20",
      "cleaning_cost": 45.50,
      "status": "completed"
    },
    "uploaded_media": {
      "images": [...],
      "videos": [...],
      "images_count": 2,
      "videos_count": 1
    },
    "cleaning_info": {
      "type": "نظافة عميقة",
      "time": "بعد النظافة"
    },
    "inventory_used": {
      "items": [...],
      "total_cost": 25.00,
      "items_count": 2
    }
  },
  "message": "تم رفع الصور والفيديوهات بعد النظافة بنجاح",
  "status": 201
}
```

---

## 🚀 **الآن API جاهز للاستخدام!**

جميع الأخطاء تم إصلاحها والـ API يعمل بشكل صحيح مع قاعدة البيانات المحدثة.
