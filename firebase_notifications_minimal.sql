-- =====================================================
-- Firebase Notifications - Minimal Setup
-- =====================================================

-- 1. إنشاء جدول الإشعارات
CREATE TABLE `notifications` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `cleaner_id` bigint(20) unsigned NOT NULL,
    `title` varchar(255) NOT NULL,
    `body` text NOT NULL,
    `type` varchar(50) NOT NULL,
    `data` json DEFAULT NULL,
    `fcm_token` varchar(255) DEFAULT NULL,
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
ALTER TABLE `cleaners` ADD COLUMN `fcm_token` varchar(255) DEFAULT NULL AFTER `image`;

-- 3. إنشاء فهارس
CREATE INDEX `cleaners_fcm_token_index` ON `cleaners` (`fcm_token`);
CREATE INDEX `cleaners_status_index` ON `cleaners` (`status`);
