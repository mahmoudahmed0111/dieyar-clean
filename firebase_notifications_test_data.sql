-- =====================================================
-- Firebase Notifications - Test Data Only
-- =====================================================

-- بيانات تجريبية لعمال النظافة
INSERT IGNORE INTO `cleaners` (`id`, `name`, `phone`, `email`, `password`, `national_id`, `address`, `hire_date`, `status`, `fcm_token`, `created_at`, `updated_at`) VALUES
(1, 'أحمد محمد', '01234567890', 'ahmed@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '12345678901234', 'القاهرة، مصر', '2024-01-01', 'active', 'fcm_token_ahmed_123', NOW(), NOW()),
(2, 'محمد علي', '01234567891', 'mohamed@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '12345678901235', 'الإسكندرية، مصر', '2024-01-02', 'active', 'fcm_token_mohamed_456', NOW(), NOW()),
(3, 'سعد حسن', '01234567892', 'saad@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '12345678901236', 'الغردقة، مصر', '2024-01-03', 'active', 'fcm_token_saad_789', NOW(), NOW()),
(4, 'خالد أحمد', '01234567893', 'khaled@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '12345678901237', 'شرم الشيخ، مصر', '2024-01-04', 'active', 'fcm_token_khaled_101', NOW(), NOW()),
(5, 'علي محمود', '01234567894', 'ali@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '12345678901238', 'مرسى علم، مصر', '2024-01-05', 'active', 'fcm_token_ali_202', NOW(), NOW());

-- بيانات تجريبية للشاليهات
INSERT IGNORE INTO `chalets` (`id`, `name`, `pass_code`, `code`, `floor`, `building`, `location`, `description`, `status`, `type`, `is_cleaned`, `is_booked`, `created_at`, `updated_at`) VALUES
(1, 'شاليه الشاطئ الذهبي', '123654ms', 'CH001', 'الأرضي', 'مبنى أ', 'شاطئ البحر الأحمر - الغردقة', 'شاليه فاخر بإطلالة مباشرة على البحر، يحتوي على غرفتي نوم، مطبخ مجهز، وتراس خاص', 'available', 'apartment', 1, 0, NOW(), NOW()),
(2, 'شاليه الفردوس', '456789ms', 'CH002', 'الأول', 'مبنى ب', 'شاطئ البحر الأحمر - الغردقة', 'شاليه مريح مع إطلالة جميلة على البحر', 'available', 'apartment', 1, 0, NOW(), NOW()),
(3, 'شاليه النجوم', '789123ms', 'CH003', 'الثاني', 'مبنى ج', 'شاطئ البحر الأحمر - الغردقة', 'شاليه حديث مع جميع المرافق الحديثة', 'available', 'apartment', 0, 1, NOW(), NOW()),
(4, 'شاليه القمر', '321654ms', 'CH004', 'الأرضي', 'مبنى د', 'شاطئ البحر الأحمر - الغردقة', 'شاليه أنيق مع إطلالة رائعة على البحر', 'available', 'apartment', 1, 0, NOW(), NOW()),
(5, 'شاليه الشمس', '654321ms', 'CH005', 'الأول', 'مبنى ه', 'شاطئ البحر الأحمر - الغردقة', 'شاليه واسع مع جميع المرافق المطلوبة', 'available', 'apartment', 0, 0, NOW(), NOW());

-- بيانات تجريبية للإشعارات
INSERT IGNORE INTO `notifications` (`id`, `cleaner_id`, `title`, `body`, `type`, `data`, `fcm_token`, `read_at`, `sent_at`, `created_at`, `updated_at`) VALUES
(1, 1, 'تنظيف منتظم جديد', 'قام أحمد محمد برفع الصور والفيديوهات قبل النظافة للشاليه: شاليه الشاطئ الذهبي', 'regular_cleaning', '{"type":"regular_cleaning","cleaning_id":1,"chalet_id":1,"chalet_name":"شاليه الشاطئ الذهبي","cleaner_name":"أحمد محمد","cleaning_time":"before","date":"2025-01-15"}', 'fcm_token_ahmed_123', NULL, NOW(), NOW(), NOW()),
(2, 2, 'بلاغ ضرر جديد', 'قام محمد علي بتسجيل بلاغ ضرر للشاليه: شاليه الفردوس', 'damage', '{"type":"damage","damage_id":1,"chalet_id":2,"chalet_name":"شاليه الفردوس","cleaner_name":"محمد علي","description":"تلف في التكييف","price":"150.00"}', 'fcm_token_mohamed_456', NOW(), NOW(), NOW(), NOW()),
(3, 3, 'تقرير صيانة جديد', 'قام سعد حسن برفع الصور والفيديوهات بعد الصيانة للشاليه: شاليه النجوم', 'maintenance', '{"type":"maintenance","maintenance_id":1,"chalet_id":3,"chalet_name":"شاليه النجوم","cleaner_name":"سعد حسن","cleaning_time":"after","description":"إصلاح التكييف"}', 'fcm_token_saad_789', NULL, NOW(), NOW(), NOW()),
(4, 4, 'تقرير مكافحة جديد', 'قام خالد أحمد برفع الصور والفيديوهات قبل المكافحة للشاليه: شاليه القمر', 'pest_control', '{"type":"pest_control","pest_control_id":1,"chalet_id":4,"chalet_name":"شاليه القمر","cleaner_name":"خالد أحمد","cleaning_time":"before","description":"مكافحة النمل"}', 'fcm_token_khaled_101', NULL, NOW(), NOW(), NOW()),
(5, 5, 'تنظيف عميق جديد', 'قام علي محمود برفع الصور والفيديوهات بعد النظافة للشاليه: شاليه الشمس', 'deep_cleaning', '{"type":"deep_cleaning","cleaning_id":2,"chalet_id":5,"chalet_name":"شاليه الشمس","cleaner_name":"علي محمود","cleaning_time":"after","date":"2025-01-15"}', 'fcm_token_ali_202', NOW(), NOW(), NOW(), NOW()),
(6, 1, 'إشعار تجريبي', 'هذا إشعار تجريبي لاختبار نظام Firebase', 'test', '{"type":"test","test_id":1,"sent_at":"2025-01-15T10:30:00Z"}', 'fcm_token_ahmed_123', NULL, NOW(), NOW(), NOW()),
(7, 2, 'إشعار تجريبي', 'هذا إشعار تجريبي لاختبار نظام Firebase', 'test', '{"type":"test","test_id":2,"sent_at":"2025-01-15T10:35:00Z"}', 'fcm_token_mohamed_456', NULL, NOW(), NOW(), NOW()),
(8, 3, 'إشعار تجريبي', 'هذا إشعار تجريبي لاختبار نظام Firebase', 'test', '{"type":"test","test_id":3,"sent_at":"2025-01-15T10:40:00Z"}', 'fcm_token_saad_789', NULL, NOW(), NOW(), NOW());

-- =====================================================
-- استعلامات للاختبار
-- =====================================================

-- عرض جميع الإشعارات مع تفاصيل عمال النظافة
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
ORDER BY n.created_at DESC;

-- إحصائيات الإشعارات حسب النوع
SELECT 
    type as 'نوع الإشعار',
    COUNT(*) as 'العدد الإجمالي',
    COUNT(CASE WHEN sent_at IS NOT NULL THEN 1 END) as 'مرسل',
    COUNT(CASE WHEN read_at IS NOT NULL THEN 1 END) as 'مقروء',
    COUNT(CASE WHEN read_at IS NULL AND sent_at IS NOT NULL THEN 1 END) as 'غير مقروء'
FROM notifications 
GROUP BY type
ORDER BY COUNT(*) DESC;

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

-- عرض أحدث 5 إشعارات
SELECT 
    n.id,
    c.name as 'اسم المنظف',
    n.title as 'العنوان',
    n.type as 'النوع',
    n.sent_at as 'تاريخ الإرسال',
    CASE 
        WHEN n.read_at IS NOT NULL THEN 'مقروء'
        WHEN n.sent_at IS NOT NULL THEN 'مرسل'
        ELSE 'في الانتظار'
    END as 'الحالة'
FROM notifications n
JOIN cleaners c ON n.cleaner_id = c.id
ORDER BY n.created_at DESC
LIMIT 5;
