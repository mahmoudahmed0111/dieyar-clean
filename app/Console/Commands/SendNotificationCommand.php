<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FirebaseNotificationService;
use App\Models\Cleaner;

class SendNotificationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notification:send
                            {title : عنوان الإشعار}
                            {body : محتوى الإشعار}
                            {--type=general : نوع الإشعار}
                            {--cleaner-id= : معرف عامل النظافة (اختياري)}
                            {--data= : بيانات إضافية (JSON)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'إرسال إشعار لعمال النظافة';

    protected $firebaseService;

    public function __construct(FirebaseNotificationService $firebaseService)
    {
        parent::__construct();
        $this->firebaseService = $firebaseService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $title = $this->argument('title');
        $body = $this->argument('body');
        $type = $this->option('type');
        $cleanerId = $this->option('cleaner-id');
        $data = $this->option('data');

        // تحويل البيانات من JSON
        $notificationData = ['type' => $type];
        if ($data) {
            $decodedData = json_decode($data, true);
            if ($decodedData) {
                $notificationData = array_merge($notificationData, $decodedData);
            }
        }

        try {
            if ($cleanerId) {
                // إرسال لعامل نظافة محدد
                $cleaner = Cleaner::find($cleanerId);
                if (!$cleaner) {
                    $this->error("عامل النظافة غير موجود");
                    return 1;
                }

                $this->firebaseService->sendToCleaner($cleaner, $title, $body, $notificationData);
                $this->info("تم إرسال الإشعار لعامل النظافة: {$cleaner->name}");
            } else {
                // إرسال لجميع عمال النظافة
                $this->firebaseService->sendToAllCleaners($title, $body, $notificationData);
                $this->info("تم إرسال الإشعار لجميع عمال النظافة");
            }

            return 0;
        } catch (\Exception $e) {
            $this->error("حدث خطأ: " . $e->getMessage());
            return 1;
        }
    }
}

