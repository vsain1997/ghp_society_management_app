<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class SendPushNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $userId;
    public $deviceId;
    public $notificationMessageArray;
    public $notificationDataArray;

    // Constructor to pass necessary data
    public function __construct($userId, $deviceId, $notificationMessageArray, $notificationDataArray = null)
    {
        $this->userId = $userId;
        $this->deviceId = $deviceId;
        $this->notificationMessageArray = $notificationMessageArray;
        $this->notificationDataArray = $notificationDataArray;
    }


    /**
     * Execute the job.
     */

    public function handle()
    {

        try {
            // Call your function to send push notification
            sendAppPushNotification(
                $this->userId,
                $this->deviceId,
                $this->notificationMessageArray,
                $this->notificationDataArray
            );

        } catch (\Exception $e) {
            \Log::error('Job failed: ' . $e->getMessage());
        }
    }

}
