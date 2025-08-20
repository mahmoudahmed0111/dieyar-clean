<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\FirebaseNotificationService;
use App\Models\Cleaner;
use Illuminate\Support\Facades\Log;

class SendNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 30;
    public $tries = 3;

    protected $title;
    protected $body;
    protected $data;
    protected $cleanerId;

    /**
     * Create a new job instance.
     */
    public function __construct($title, $body, $data = [], $cleanerId = null)
    {
        $this->title = $title;
        $this->body = $body;
        $this->data = $data;
        $this->cleanerId = $cleanerId;
    }

    /**
     * Execute the job.
     */
    public function handle(FirebaseNotificationService $firebaseService): void
    {
        try {
            if ($this->cleanerId) {
                // إرسال لعامل نظافة محدد
                $cleaner = Cleaner::find($this->cleanerId);
                if ($cleaner) {
                    $firebaseService->sendToCleaner($cleaner, $this->title, $this->body, $this->data);
                }
            } else {
                // إرسال لجميع عمال النظافة
                $firebaseService->sendToAllCleaners($this->title, $this->body, $this->data);
            }
        } catch (\Exception $e) {
            // تسجيل الخطأ وإعادة المحاولة
            Log::error('Notification job failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Notification job failed permanently: ' . $exception->getMessage());
    }
}
