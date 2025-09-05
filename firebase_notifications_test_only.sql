-- =====================================================
-- Firebase Notifications - Test Data Only
-- =====================================================

-- إدراج إشعارات تجريبية
INSERT INTO `notifications` (`cleaner_id`, `title`, `body`, `type`, `data`, `fcm_token`, `sent_at`, `created_at`, `updated_at`) VALUES
(1, 'تنظيف منتظم جديد', 'قام أحمد محمد برفع الصور والفيديوهات قبل النظافة للشاليه: شاليه الشاطئ الذهبي', 'regular_cleaning', '{"type":"regular_cleaning","cleaning_id":1,"chalet_id":1,"chalet_name":"شاليه الشاطئ الذهبي","cleaner_name":"أحمد محمد","cleaning_time":"before","date":"2025-01-15"}', 'fcm_token_ahmed_123', NOW(), NOW(), NOW()),
(2, 'بلاغ ضرر جديد', 'قام محمد علي بتسجيل بلاغ ضرر للشاليه: شاليه الفردوس', 'damage', '{"type":"damage","damage_id":1,"chalet_id":2,"chalet_name":"شاليه الفردوس","cleaner_name":"محمد علي","description":"تلف في التكييف","price":"150.00"}', 'fcm_token_mohamed_456', NOW(), NOW(), NOW()),
(3, 'تقرير صيانة جديد', 'قام سعد حسن برفع الصور والفيديوهات بعد الصيانة للشاليه: شاليه النجوم', 'maintenance', '{"type":"maintenance","maintenance_id":1,"chalet_id":3,"chalet_name":"شاليه النجوم","cleaner_name":"سعد حسن","cleaning_time":"after","description":"إصلاح التكييف"}', 'fcm_token_saad_789', NOW(), NOW(), NOW()),
(4, 'تقرير مكافحة جديد', 'قام خالد أحمد برفع الصور والفيديوهات قبل المكافحة للشاليه: شاليه القمر', 'pest_control', '{"type":"pest_control","pest_control_id":1,"chalet_id":4,"chalet_name":"شاليه القمر","cleaner_name":"خالد أحمد","cleaning_time":"before","description":"مكافحة النمل"}', 'fcm_token_khaled_101', NOW(), NOW(), NOW()),
(5, 'تنظيف عميق جديد', 'قام علي محمود برفع الصور والفيديوهات بعد النظافة للشاليه: شاليه الشمس', 'deep_cleaning', '{"type":"deep_cleaning","cleaning_id":2,"chalet_id":5,"chalet_name":"شاليه الشمس","cleaner_name":"علي محمود","cleaning_time":"after","date":"2025-01-15"}', 'fcm_token_ali_202', NOW(), NOW(), NOW());

-- تحديث FCM tokens لعمال النظافة
UPDATE `cleaners` SET `fcm_token` = 'fcm_token_ahmed_123' WHERE `id` = 1;
UPDATE `cleaners` SET `fcm_token` = 'fcm_token_mohamed_456' WHERE `id` = 2;
UPDATE `cleaners` SET `fcm_token` = 'fcm_token_saad_789' WHERE `id` = 3;
UPDATE `cleaners` SET `fcm_token` = 'fcm_token_khaled_101' WHERE `id` = 4;
UPDATE `cleaners` SET `fcm_token` = 'fcm_token_ali_202' WHERE `id` = 5;
