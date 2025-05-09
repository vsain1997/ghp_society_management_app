<?php

namespace App\Console\Commands;

use App\Models\Bill;
use App\Models\BillService;
use App\Models\Member;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CreateMaintenanceBills extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-maintenance-bills';

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
        $members = Member::whereStatus('active')->get();
        $defaultService = BillService::whereName('Maintenance')->first();
        $creator = User::whereRole('super_admin')->first();

        foreach($members as $member){
            Bill::create([
                'user_id' => $member->user->id,
                'service_id' => $defaultService->id,
                'bill_type' => 'maintenance',
                'amount' => $member->maintenance_bill ?? 0,
                'due_date' => Carbon::now()->startOfMonth()->addDays(9),  //10th of current month
                'society_id' => $member->society_id,
                'created_by' => $creator->id,
            ]);
        }
    }
}
