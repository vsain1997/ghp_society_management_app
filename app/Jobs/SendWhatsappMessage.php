<?php

namespace App\Jobs;

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

    /**
     * Create a new job instance.
     */
    public function __construct($data, $event)
    {
        $this->data = $data;
        $this->event = $event;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        if($this->event == 'bill_reminder'){

        }
    }
}
