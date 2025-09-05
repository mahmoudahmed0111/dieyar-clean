-- =====================================================
-- Firebase Notifications - Quick Setup SQL
-- =====================================================

-- 1. إضافة عمود FCM Token لجدول cleaners
ALTER TABLE `cleaners` 
ADD COLUMN IF NOT EXISTS `fcm_token` varchar(255) DEFAULT NULL COMMENT 'Firebase Cloud Messaging Token' AFTER `image`;

-- 2. إنشاء جدول الإشعارات
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

-- 3. إنشاء فهارس للأداء
CREATE INDEX IF NOT EXISTS `cleaners_fcm_token_index` ON `cleaners` (`fcm_token`);
CREATE INDEX IF NOT EXISTS `cleaners_status_index` ON `cleaners` (`status`);

-- 4. إدراج بيانات تجريبية
INSERT IGNORE INTO `cleaners` (`id`, `name`, `phone`, `email`, `password`, `national_id`, `address`, `hire_date`, `status`, `created_at`, `updated_at`) VALUES
(1, 'أحمد محمد', '01234567890', 'ahmed@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '12345678901234', 'القاهرة، مصر', '2024-01-01', 'active', NOW(), NOW()),
(2, 'محمد علي', '01234567891', 'mohamed@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '12345678901235', 'الإسكندرية، مصر', '2024-01-02', 'active', NOW(), NOW()),
(3, 'سعد حسن', '01234567892', 'saad@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '12345678901236', 'الغردقة، مصر', '2024-01-03', 'active', NOW(), NOW());

INSERT IGNORE INTO `chalets` (`id`, `name`, `pass_code`, `code`, `floor`, `building`, `location`, `description`, `status`, `type`, `is_cleaned`, `is_booked`, `created_at`, `updated_at`) VALUES
(1, 'شاليه الشاطئ الذهبي', '123654ms', 'CH001', 'الأرضي', 'مبنى أ', 'شاطئ البحر الأحمر - الغردقة', 'شاليه فاخر بإطلالة مباشرة على البحر', 'available', 'apartment', 1, 0, NOW(), NOW()),
(2, 'شاليه الفردوس', '456789ms', 'CH002', 'الأول', 'مبنى ب', 'شاطئ البحر الأحمر - الغردقة', 'شاليه مريح مع إطلالة جميلة على البحر', 'available', 'apartment', 1, 0, NOW(), NOW());

-- 5. إدراج إشعارات تجريبية
INSERT IGNORE INTO `notifications` (`id`, `cleaner_id`, `title`, `body`, `type`, `data`, `sent_at`, `created_at`, `updated_at`) VALUES
(1, 1, 'تنظيف منتظم جديد', 'قام أحمد محمد برفع الصور والفيديوهات قبل النظافة للشاليه: شاليه الشاطئ الذهبي', 'regular_cleaning', '{"type":"regular_cleaning","cleaning_id":1,"chalet_id":1,"chalet_name":"شاليه الشاطئ الذهبي","cleaner_name":"أحمد محمد","cleaning_time":"before","date":"2025-01-15"}', NOW(), NOW(), NOW()),
(2, 2, 'بلاغ ضرر جديد', 'قام محمد علي بتسجيل بلاغ ضرر للشاليه: شاليه الفردوس', 'damage', '{"type":"damage","damage_id":1,"chalet_id":2,"chalet_name":"شاليه الفردوس","cleaner_name":"محمد علي","description":"تلف في التكييف","price":"150.00"}', NOW(), NOW(), NOW());

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
WHERE read_at IS NOT NULL;

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
WHERE status = 'active' AND fcm_token IS NOT NULL;
