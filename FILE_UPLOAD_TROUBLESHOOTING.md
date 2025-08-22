# دليل استكشاف أخطاء رفع الملفات

## المشكلة
الملفات (الصور والفيديوهات) لا يتم رفعها رغم نجاح الطلب.

## الأسباب المحتملة والحلول

### 1. مشكلة في Postman

#### المشكلة:
- علامات تحذير صفراء بجانب الملفات في Postman
- الملفات لا يتم إرسالها بشكل صحيح

#### الحل:
1. **تأكد من نوع الملف:**
   - في Postman، اختر `File` type (وليس Text)
   - انقر على `Select Files` واختر الملفات الفعلية

2. **تأكد من اسم الحقل:**
   ```
   images[] (وليس images)
   videos[] (وليس videos)
   ```

3. **إعادة تشغيل Postman:**
   - أغلق Postman تماماً
   - أعد فتحه
   - أعد إنشاء الطلب

### 2. مشكلة في إعدادات الخادم

#### التحقق من storage link:
```bash
php artisan storage:link
```

#### التحقق من الصلاحيات:
```bash
chmod -R 755 storage/
chmod -R 755 public/storage/
```

### 3. مشكلة في حجم الملفات

#### التحقق من إعدادات PHP:
في ملف `php.ini`:
```ini
upload_max_filesize = 100M
post_max_size = 100M
max_execution_time = 300
memory_limit = 256M
```

#### التحقق من إعدادات Laravel:
في ملف `.env`:
```env
FILESYSTEM_DISK=public
```

### 4. اختبار بسيط

#### إنشاء ملف اختبار:
```php
// في DamageController، أضف هذا الكود مؤقتاً للاختبار
public function testUpload(Request $request)
{
    $debug = [
        'all_data' => $request->all(),
        'has_files' => $request->hasFile('images'),
        'files_count' => $request->hasFile('images') ? count($request->file('images')) : 0,
        'file_names' => $request->hasFile('images') ? array_map(function($file) {
            return $file->getClientOriginalName();
        }, $request->file('images')) : [],
    ];
    
    return response()->json($debug);
}
```

### 5. خطوات التشخيص

#### 1. تحقق من السجلات:
```bash
tail -f storage/logs/laravel.log
```

#### 2. تحقق من الاستجابة الجديدة:
الآن API سيعيد معلومات تشخيص في `debug_info`:
```json
{
    "debug_info": {
        "has_images_in_request": true/false,
        "has_videos_in_request": true/false,
        "images_count_in_request": 0,
        "videos_count_in_request": 0
    }
}
```

#### 3. تحقق من مجلد storage:
```bash
ls -la storage/app/public/
ls -la public/storage/
```

### 6. حلول بديلة

#### الحل الأول: استخدام cURL
```bash
curl -X POST \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -F "chalet_id=1" \
  -F "description=test" \
  -F "price=100" \
  -F "images[]=@/path/to/image.jpg" \
  -F "videos[]=@/path/to/video.mp4" \
  http://127.0.0.1:8000/api/damages/upload
```

#### الحل الثاني: استخدام JavaScript
```javascript
const formData = new FormData();
formData.append('chalet_id', '1');
formData.append('description', 'test');
formData.append('price', '100');

// إضافة ملفات
const imageFile = document.getElementById('imageInput').files[0];
const videoFile = document.getElementById('videoInput').files[0];

formData.append('images[]', imageFile);
formData.append('videos[]', videoFile);

fetch('/api/damages/upload', {
    method: 'POST',
    headers: {
        'Authorization': 'Bearer ' + token
    },
    body: formData
})
.then(response => response.json())
.then(data => console.log(data));
```

### 7. نصائح إضافية

1. **تأكد من وجود الملفات:**
   - تحقق من أن الملفات موجودة في المسار المحدد
   - تأكد من أن الملفات ليست فارغة

2. **تحقق من نوع الملف:**
   - الصور: jpg, jpeg, png, gif, webp
   - الفيديوهات: mp4, avi, mov, wmv, webm

3. **تحقق من حجم الملف:**
   - الصور: حتى 10MB
   - الفيديوهات: حتى 100MB

4. **إعادة تشغيل الخادم:**
   ```bash
   php artisan serve
   ```

### 8. اختبار سريع

#### في Postman:
1. أنشئ طلب جديد
2. اختر `POST`
3. أدخل الرابط: `http://127.0.0.1:8000/api/damages/upload`
4. اختر `Body` → `form-data`
5. أضف الحقول:
   - `chalet_id`: Text, Value: `1`
   - `description`: Text, Value: `test`
   - `price`: Text, Value: `100`
   - `images[]`: **File**, اختر ملف صورة حقيقي
   - `videos[]`: **File**, اختر ملف فيديو حقيقي
6. أضف Header: `Authorization: Bearer YOUR_TOKEN`
7. أرسل الطلب

#### النتيجة المتوقعة:
```json
{
    "debug_info": {
        "has_images_in_request": true,
        "has_videos_in_request": true,
        "images_count_in_request": 1,
        "videos_count_in_request": 1
    }
}
```

إذا كانت النتيجة `false` أو `0`، فهناك مشكلة في إرسال الملفات من Postman.
