<?php

namespace App\Jobs;

use App\FacebookApi;
use App\Models\BillWhatsappLog;
use Carbon\Carbon;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendWhatsappMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $data;
    public $event;
    public $fb;

    /**
     * Create a new job instance.
     */
    public function __construct($data, $event)
    {
        $this->data = $data;
        $this->event = $event;

        $this->fb = new FacebookApi();
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        if($this->event == 'new_bill_create'){
            $user = isset($this->data->user) ? $this->data->user : null;
            $service = isset($this->data->service) ? $this->data->service : null;
            $member = isset($this->data->user->member) ? $this->data->user->member : null;

            try{
                if($user && $member && $service){
                    $messageVeriables = [
                        $user->name,
                        $service->name,
                        toRupeeCurrency($this->data->amount),
                        'will be due on '. formateDate2($this->data->due_date),
                        'on '. formateDate2(Carbon::parse($this->data->due_date)->addDays(2)),
                    ];

                    $perameters = generateNormalParameters($messageVeriables);
                    $msgData = createNormalTemplateMessageData($member->phone, 'bill_reminder_updated', 'en', $perameters);
                    $resp = $this->fb->sendMessage($msgData);

                    if($resp['status']){
                        BillWhatsappLog::create([
                            'user_id' => $user->id,
                            'bill_id' => $this->data->id,
                            'status' => 'sent',
                            'details' => json_encode($resp['result'], true)
                        ]);
                    }else{
                        BillWhatsappLog::create([
                            'user_id' => $user->id,
                            'bill_id' => $this->data->id,
                            'status' => 'failed',
                            'details' => json_encode($resp['result'], true)
                        ]);
                    }
                }
            } catch (Exception $e){
                BillWhatsappLog::create([
                    'user_id' => $user->id,
                    'bill_id' => $this->data->id,
                    'status' => 'failed',
                    'details' => $e->getMessage()
                ]);
            }

        }
    }
}
