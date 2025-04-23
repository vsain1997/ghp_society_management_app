<?php

namespace App\Console\Commands;

use App\FacebookApi;
use App\Models\Bill;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendBillReminders extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-bill-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $fbObj = new FacebookApi();
        $canSendMessage = canSendMessage('whatsapp_message');
        if($canSendMessage['status']){
            $this->line('Can Send Message');
            // Chunk the bills to process them in manageable batches
            Bill::whereStatus('unpaid')->chunk(env("CHUNK_SIZE"), function ($bills) use($fbObj){
                foreach ($bills as $bill) {
                    $calculatedDate = $bill->due_date;

                    $user = $bill->user;
                    $member = $bill->user->member;
                    $service = $bill->service;
                    $dueDate = $bill->due_date;
                    $today = Carbon::today();


                    $daysDifference = Carbon::today()->diffInDays($calculatedDate, false);

                    $isBefore7Days = Carbon::parse($calculatedDate)->copy()->subDays(7);
                    $isBefore2Days = Carbon::parse($calculatedDate)->copy()->subDays(2);
                    $overDue1Day = Carbon::parse($bill->due_date)->addDays(1)->startOfDay();
                    $overDue2Day = Carbon::parse($bill->due_date)->addDays(2)->startOfDay();
                    $overDue3Day = Carbon::parse($bill->due_date)->addDays(3)->startOfDay();

                    $isBefore0Days = Carbon::parse($calculatedDate)->copy();

                    // Reminder 7 or 2 days before due date
                    if (Carbon::today()->isSameDay($isBefore7Days) || Carbon::today()->isSameDay($isBefore2Days)) {
                        $template = "society_bill_reminder_before_some_days";
                        $messageVeriables = [
                            ucwords($user->name),
                            $service->name,
                            formateDate2($bill->due_date),
                            toRupeeCurrency($bill->amount)
                        ];

                        $perameters = generateNormalParameters($messageVeriables);
                        $msgData = createNormalTemplateMessageData($member->phone, $template, 'en', $perameters);
                        $fbObj->sendMessage($msgData);
                    }

                    // Reminder on due date
                    if (Carbon::today()->isSameDay($isBefore0Days)) {
                        $template = "society_bill_reminder_on_due_date";
                        $messageVeriables = [
                            ucwords($user->name),
                            $service->name,
                            toRupeeCurrency($bill->amount)
                        ];
                        $perameters = generateNormalParameters($messageVeriables);
                        $msgData = createNormalTemplateMessageData($member->phone, $template, 'en', $perameters);
                        $fbObj->sendMessage($msgData);
                    }


                    // AFTER OVERDUE - 1 DAY
                    if ($today->equalTo($overDue1Day)) {
                        $template = "bill_overdue_1_day";
                        $messageVeriables = [
                            ucwords($user->name),
                            toRupeeCurrency($bill->amount)
                        ];
                        $perameters = generateNormalParameters($messageVeriables);
                        $msgData = createNormalTemplateMessageData($member->phone, $template, 'en', $perameters);
                        $fbObj->sendMessage($msgData);
                    }

                    // AFTER OVERDUE - 2 DAY
                    if ($today->equalTo($overDue2Day)) {
                        $template = "society_bill_reminder_last_day";
                        $messageVeriables = [
                            ucwords($user->name),
                        ];
                        $perameters = generateNormalParameters($messageVeriables);
                        $msgData = createNormalTemplateMessageData($member->phone, $template, 'en', $perameters);
                        $fbObj->sendMessage($msgData);
                    }

                    // AFTER OVERDUE - 3 DAY
                    if ($today->equalTo($overDue3Day)) {
                        $template = "society_bill_overdue_restriction";
                        $messageVeriables = [
                            ucwords($user->name),
                            toRupeeCurrency($bill->amount)
                        ];
                        $perameters = generateNormalParameters($messageVeriables);
                        $msgData = createNormalTemplateMessageData($member->phone, $template, 'en', $perameters);
                        $fbObj->sendMessage($msgData);
                    }
                }
            });
        }else{
            $this->line($canSendMessage['message']);
        }
    }
}
