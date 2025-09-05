-- =====================================================
-- Firebase Notifications - Notifications Table Only
-- =====================================================

-- 1. إنشاء جدول الإشعارات
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

-- 2. إضافة عمود FCM Token لجدول cleaners
ALTER TABLE `cleaners` 
ADD COLUMN IF NOT EXISTS `fcm_token` varchar(255) DEFAULT NULL COMMENT 'Firebase Cloud Messaging Token' AFTER `image`;

-- 3. إنشاء فهارس للأداء
CREATE INDEX IF NOT EXISTS `cleaners_fcm_token_index` ON `cleaners` (`fcm_token`);
CREATE INDEX IF NOT EXISTS `cleaners_status_index` ON `cleaners` (`status`);

-- =====================================================
-- بيانات تجريبية للإشعارات
-- =====================================================

-- 4. إدراج إشعارات تجريبية
INSERT IGNORE INTO `notifications` (`id`, `cleaner_id`, `title`, `body`, `type`, `data`, `fcm_token`, `read_at`, `sent_at`, `created_at`, `updated_at`) VALUES
(1, 1, 'تنظيف منتظم جديد', 'قام أحمد محمد برفع الصور والفيديوهات قبل النظافة للشاليه: شاليه الشاطئ الذهبي', 'regular_cleaning', '{"type":"regular_cleaning","cleaning_id":1,"chalet_id":1,"chalet_name":"شاليه الشاطئ الذهبي","cleaner_name":"أحمد محمد","cleaning_time":"before","date":"2025-01-15"}', 'fcm_token_ahmed_123', NULL, NOW(), NOW(), NOW()),
(2, 2, 'بلاغ ضرر جديد', 'قام محمد علي بتسجيل بلاغ ضرر للشاليه: شاليه الفردوس', 'damage', '{"type":"damage","damage_id":1,"chalet_id":2,"chalet_name":"شاليه الفردوس","cleaner_name":"محمد علي","description":"تلف في التكييف","price":"150.00"}', 'fcm_token_mohamed_456', NOW(), NOW(), NOW(), NOW()),
(3, 3, 'تقرير صيانة جديد', 'قام سعد حسن برفع الصور والفيديوهات بعد الصيانة للشاليه: شاليه النجوم', 'maintenance', '{"type":"maintenance","maintenance_id":1,"chalet_id":3,"chalet_name":"شاليه النجوم","cleaner_name":"سعد حسن","cleaning_time":"after","description":"إصلاح التكييف"}', 'fcm_token_saad_789', NULL, NOW(), NOW(), NOW()),
(4, 4, 'تقرير مكافحة جديد', 'قام خالد أحمد برفع الصور والفيديوهات قبل المكافحة للشاليه: شاليه القمر', 'pest_control', '{"type":"pest_control","pest_control_id":1,"chalet_id":4,"chalet_name":"شاليه القمر","cleaner_name":"خالد أحمد","cleaning_time":"before","description":"مكافحة النمل"}', 'fcm_token_khaled_101', NULL, NOW(), NOW(), NOW()),
(5, 5, 'تنظيف عميق جديد', 'قام علي محمود برفع الصور والفيديوهات بعد النظافة للشاليه: شاليه الشمس', 'deep_cleaning', '{"type":"deep_cleaning","cleaning_id":2,"chalet_id":5,"chalet_name":"شاليه الشمس","cleaner_name":"علي محمود","cleaning_time":"after","date":"2025-01-15"}', 'fcm_token_ali_202', NOW(), NOW(), NOW(), NOW());

-- =====================================================
-- استعلامات المراقبة
-- =====================================================

-- إحصائيات الإشعارات
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
WHERE read_at IS NULL AND sent_at IS NOT NULL;

-- إحصائيات عمال النظافة
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

-- عرض الإشعارات حسب النوع
SELECT 
    type as 'نوع الإشعار',
    COUNT(*) as 'العدد الإجمالي',
    COUNT(CASE WHEN sent_at IS NOT NULL THEN 1 END) as 'مرسل',
    COUNT(CASE WHEN read_at IS NOT NULL THEN 1 END) as 'مقروء'
FROM notifications 
GROUP BY type
ORDER BY COUNT(*) DESC;

-- عرض أحدث الإشعارات
SELECT 
    n.id,
    c.name as 'اسم المنظف',
    n.title as 'العنوان',
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
