<?php

namespace App\Console\Commands;

use App\Models\Member;
use App\Models\Sos;
use App\Models\Staff;
use Illuminate\Console\Command;

class SendSosAlerts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sos:send-alerts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send repeated SOS alerts every 30 seconds until acknowledged';

    /**
     * Execute the console command.
     */

    public function handle()
    {
        $alerts = Sos::with('user')->where('status', 'new')->get();

        foreach ($alerts as $alert) {
            $notifyBody = '';
            $guardsQuery = Staff::where('society_id', $alert->society_id)
                ->whereHas('user', function ($query) use ($alert) {
                    $query->whereNotNull('device_id');
                });

            if ($alert->user->role == 'admin' || $alert->user->role == 'resident') {
                $sosSender = Member::with('block')->where('user_id', $alert->alert_by)->first();

                if ($sosSender) {

                    $notifyBody = $alert->user->name . "( Block : " . $sosSender->block->name . ", Floor : " . $sosSender->block->floor . ", Property Number : " . $sosSender->block->property_number . ", Area - " . $alert->area . " ) has sent an SOS. Description - " . $alert->description . " . Please respond immediately";
                }
            } elseif ($alert->user->role == 'staff_security_guard') {
                $sosSender = Staff::where('user_id', $alert->alert_by)->first();

                if ($sosSender) {
                    $sosSender = Staff::where('user_id', $alert->alert_by)->first();
                    $notifyBody = $alert->user->name . "( Area - " . $alert->area . " ) has sent an SOS. Description - " . $alert->description . " . Please respond immediately";
                }

                // Exclude the sender from recipients
                $guardsQuery->whereHas('user', function ($query) use ($alert) {
                    $query->where('id', '!=', $alert->alert_by);
                });
            }

            // Fetch guards with device IDs
            $guards = $guardsQuery->with('user:id,device_id')->get();

            foreach ($guards as $guard) {
                if (!empty($guard->user->device_id)) {
                    $notificationMessageArray = [
                        'title' => 'Emergency SOS Alert',
                        'body' => $notifyBody,
                    ];

                    $notificationDataArray = [
                        'type' => 'sos_alert',
                        'sos_id' => $alert->id,
                        'name' => $alert->user->name,
                        'mob' => $alert->user->phone,
                        'user_id' => $alert->user->id,
                        'user_type' => $alert->user->role,
                        'img' => $alert->user->image ?? null,
                        'time' => now()->format('h:iA'),
                    ];

                    \Log::info("<to security guard - message > ||JOB-SCHEDULE||:::");
                    $stopInAppNotification = 0; // as users.id=0 does not exist, so in app notification wont be sent to anyone.
                    sendAppPushNotification($stopInAppNotification, $guard->user->device_id, $notificationMessageArray, $notificationDataArray);
                }
            }
        }
    }

}
