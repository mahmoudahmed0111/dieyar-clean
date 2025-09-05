-- =====================================================
-- Firebase Notifications Database Setup for DieyarClean
-- =====================================================

-- 1. إنشاء جدول الإشعارات (إذا لم يكن موجوداً)
CREATE TABLE IF NOT EXISTS `notifications` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `cleaner_id` bigint(20) unsigned NOT NULL,
    `title` varchar(255) NOT NULL,
    `body` text NOT NULL,
    `type` varchar(50) NOT NULL COMMENT 'regular_cleaning, deep_cleaning, maintenance, pest_control, damage',
    `data` json DEFAULT NULL COMMENT 'بيانات إضافية',
    `fcm_token` varchar(255) DEFAULT NULL COMMENT 'رمز FCM للجهاز',
    `read_at` timestamp NULL DEFAULT NULL,
    `sent_at` timestamp NULL DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `notifications_cleaner_id_read_at_index` (`cleaner_id`, `read_at`),
    KEY `notifications_type_index` (`type`),
    CONSTRAINT `notifications_cleaner_id_foreign` FOREIGN KEY (`cleaner_id`) REFERENCES `cleaners` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. إضافة عمود FCM Token لجدول cleaners (إذا لم يكن موجوداً)
ALTER TABLE `cleaners` 
ADD COLUMN IF NOT EXISTS `fcm_token` varchar(255) DEFAULT NULL COMMENT 'Firebase Cloud Messaging Token' AFTER `image`;

-- 3. إنشاء فهرس لعمود FCM Token
CREATE INDEX IF NOT EXISTS `cleaners_fcm_token_index` ON `cleaners` (`fcm_token`);

-- 4. إنشاء فهرس لعمود status في cleaners
CREATE INDEX IF NOT EXISTS `cleaners_status_index` ON `cleaners` (`status`);

-- =====================================================
-- بيانات تجريبية للاختبار
-- =====================================================

-- 5. إدراج عمال نظافة تجريبيين (إذا لم يكونوا موجودين)
INSERT IGNORE INTO `cleaners` (`id`, `name`, `phone`, `email`, `password`, `national_id`, `address`, `hire_date`, `status`, `image`, `fcm_token`, `created_at`, `updated_at`) VALUES
(1, 'أحمد محمد', '01234567890', 'ahmed@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '12345678901234', 'القاهرة، مصر', '2024-01-01', 'active', NULL, NULL, NOW(), NOW()),
(2, 'محمد علي', '01234567891', 'mohamed@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '12345678901235', 'الإسكندرية، مصر', '2024-01-02', 'active', NULL, NULL, NOW(), NOW()),
(3, 'سعد حسن', '01234567892', 'saad@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '12345678901236', 'الغردقة، مصر', '2024-01-03', 'active', NULL, NULL, NOW(), NOW()),
(4, 'خالد أحمد', '01234567893', 'khaled@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '12345678901237', 'شرم الشيخ، مصر', '2024-01-04', 'active', NULL, NULL, NOW(), NOW()),
(5, 'علي محمود', '01234567894', 'ali@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '12345678901238', 'مرسى علم، مصر', '2024-01-05', 'active', NULL, NULL, NOW(), NOW());

-- 6. إدراج شاليهات تجريبية (إذا لم تكن موجودة)
INSERT IGNORE INTO `chalets` (`id`, `name`, `pass_code`, `code`, `floor`, `building`, `location`, `description`, `status`, `type`, `is_cleaned`, `is_booked`, `created_at`, `updated_at`) VALUES
(1, 'شاليه الشاطئ الذهبي', '123654ms', 'CH001', 'الأرضي', 'مبنى أ', 'شاطئ البحر الأحمر - الغردقة', 'شاليه فاخر بإطلالة مباشرة على البحر، يحتوي على غرفتي نوم، مطبخ مجهز، وتراس خاص', 'available', 'apartment', 1, 0, NOW(), NOW()),
(2, 'شاليه الفردوس', '456789ms', 'CH002', 'الأول', 'مبنى ب', 'شاطئ البحر الأحمر - الغردقة', 'شاليه مريح مع إطلالة جميلة على البحر', 'available', 'apartment', 1, 0, NOW(), NOW()),
(3, 'شاليه النجوم', '789123ms', 'CH003', 'الثاني', 'مبنى ج', 'شاطئ البحر الأحمر - الغردقة', 'شاليه حديث مع جميع المرافق الحديثة', 'available', 'apartment', 0, 1, NOW(), NOW());

-- =====================================================
-- إشعارات تجريبية للاختبار
-- =====================================================

-- 7. إدراج إشعارات تجريبية
INSERT IGNORE INTO `notifications` (`id`, `cleaner_id`, `title`, `body`, `type`, `data`, `fcm_token`, `read_at`, `sent_at`, `created_at`, `updated_at`) VALUES
(1, 1, 'تنظيف منتظم جديد', 'قام أحمد محمد برفع الصور والفيديوهات قبل النظافة للشاليه: شاليه الشاطئ الذهبي', 'regular_cleaning', '{"type":"regular_cleaning","cleaning_id":1,"chalet_id":1,"chalet_name":"شاليه الشاطئ الذهبي","cleaner_name":"أحمد محمد","cleaning_time":"before","date":"2025-01-15"}', NULL, NULL, NOW(), NOW(), NOW()),
(2, 2, 'بلاغ ضرر جديد', 'قام محمد علي بتسجيل بلاغ ضرر للشاليه: شاليه الفردوس', 'damage', '{"type":"damage","damage_id":1,"chalet_id":2,"chalet_name":"شاليه الفردوس","cleaner_name":"محمد علي","description":"تلف في التكييف","price":"150.00"}', NULL, NULL, NOW(), NOW(), NOW()),
(3, 3, 'تقرير صيانة جديد', 'قام سعد حسن برفع الصور والفيديوهات بعد الصيانة للشاليه: شاليه النجوم', 'maintenance', '{"type":"maintenance","maintenance_id":1,"chalet_id":3,"chalet_name":"شاليه النجوم","cleaner_name":"سعد حسن","cleaning_time":"after","description":"إصلاح التكييف"}', NULL, NULL, NOW(), NOW(), NOW());

-- =====================================================
-- استعلامات مفيدة للمراقبة
-- =====================================================

-- 8. عرض إحصائيات الإشعارات
SELECT 
    'إجمالي الإشعارات' as metric,
    COUNT(*) as count
FROM notifications
UNION ALL
SELECT 
    'الإشعارات المرسلة' as metric,
    COUNT(*) as count
FROM notifications 
WHERE sent_at IS NOT NULL
UNION ALL
SELECT 
    'الإشعارات المقروءة' as metric,
    COUNT(*) as count
FROM notifications 
WHERE read_at IS NOT NULL
UNION ALL
SELECT 
    'الإشعارات غير المقروءة' as metric,
    COUNT(*) as count
FROM notifications 
WHERE read_at IS NULL;

-- 9. عرض إحصائيات عمال النظافة
SELECT 
    'إجمالي عمال النظافة النشطين' as metric,
    COUNT(*) as count
FROM cleaners 
WHERE status = 'active'
UNION ALL
SELECT 
    'عمال النظافة الذين لديهم FCM Token' as metric,
    COUNT(*) as count
FROM cleaners 
WHERE status = 'active' AND fcm_token IS NOT NULL
UNION ALL
SELECT 
    'عمال النظافة الذين لا يملكون FCM Token' as metric,
    COUNT(*) as count
FROM cleaners 
WHERE status = 'active' AND fcm_token IS NULL;

-- 10. عرض الإشعارات حسب النوع
SELECT 
    type as 'نوع الإشعار',
    COUNT(*) as 'العدد',
    COUNT(CASE WHEN sent_at IS NOT NULL THEN 1 END) as 'مرسل',
    COUNT(CASE WHEN read_at IS NOT NULL THEN 1 END) as 'مقروء'
FROM notifications 
GROUP BY type
ORDER BY COUNT(*) DESC;

-- 11. عرض أحدث الإشعارات
SELECT 
    n.id,
    c.name as 'اسم المنظف',
    n.title as 'العنوان',
    n.body as 'المحتوى',
    n.type as 'النوع',
    n.sent_at as 'تاريخ الإرسال',
    n.read_at as 'تاريخ القراءة',
    CASE 
        WHEN n.read_at IS NOT NULL THEN 'مقروء'
        WHEN n.sent_at IS NOT NULL THEN 'مرسل'
        ELSE 'في الانتظار'
    END as 'الحالة'
FROM notifications n
JOIN cleaners c ON n.cleaner_id = c.id
ORDER BY n.created_at DESC
LIMIT 10;

-- =====================================================
-- إعدادات Firebase (يجب إضافتها إلى ملف .env)
-- =====================================================

/*
FIREBASE_PROJECT_ID=deiyar
FIREBASE_SERVER_KEY=your_firebase_server_key_here

ملاحظة: 
- يجب وضع ملف firebase-service-account.json في storage/app/
- يمكن الحصول على Server Key من Firebase Console > Project Settings > Cloud Messaging
*/

-- =====================================================
-- اختبار النظام
-- =====================================================

-- 12. اختبار إرسال إشعار تجريبي
-- يمكن استخدام API التالي لاختبار النظام:
-- POST /api/test/notification
-- {
--     "title": "إشعار تجريبي",
--     "body": "هذا إشعار تجريبي لاختبار النظام",
--     "cleaner_id": 1
-- }

-- 13. اختبار فحص إعدادات Firebase
-- يمكن استخدام API التالي لفحص الإعدادات:
-- GET /api/test/firebase-config

-- =====================================================
-- تنظيف البيانات التجريبية (اختياري)
-- =====================================================

-- 14. حذف البيانات التجريبية (إذا كنت تريد بداية نظيفة)
-- DELETE FROM notifications WHERE id IN (1, 2, 3);
-- DELETE FROM cleaners WHERE id IN (1, 2, 3, 4, 5);
-- DELETE FROM chalets WHERE id IN (1, 2, 3);

-- =====================================================
-- نهاية ملف SQL
-- =====================================================
