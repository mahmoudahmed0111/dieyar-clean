-- =====================================================
-- Firebase Notifications - Clean Test Data
-- =====================================================

-- تحذير: هذا الملف يحذف البيانات التجريبية فقط
-- تأكد من عدم وجود بيانات مهمة قبل التشغيل

-- 1. حذف الإشعارات التجريبية
DELETE FROM notifications WHERE id IN (1, 2, 3, 4, 5, 6, 7, 8);

-- 2. حذف عمال النظافة التجريبيين
DELETE FROM cleaners WHERE id IN (1, 2, 3, 4, 5);

-- 3. حذف الشاليهات التجريبية
DELETE FROM chalets WHERE id IN (1, 2, 3, 4, 5);

-- 4. إعادة تعيين AUTO_INCREMENT
ALTER TABLE notifications AUTO_INCREMENT = 1;
ALTER TABLE cleaners AUTO_INCREMENT = 1;
ALTER TABLE chalets AUTO_INCREMENT = 1;

-- 5. تنظيف FCM Tokens (اختياري)
-- UPDATE cleaners SET fcm_token = NULL WHERE fcm_token IS NOT NULL;

-- 6. عرض النتيجة
SELECT 'تم حذف البيانات التجريبية بنجاح' as message;

-- عرض عدد السجلات المتبقية
SELECT 
    'الإشعارات المتبقية' as table_name,
    COUNT(*) as count
FROM notifications
UNION ALL
SELECT 
    'عمال النظافة المتبقين' as table_name,
    COUNT(*) as count
FROM cleaners
UNION ALL
SELECT 
    'الشاليهات المتبقية' as table_name,
    COUNT(*) as count
FROM chalets;
