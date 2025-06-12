<?php

namespace App\Imports;

use App\Models\Block;
use App\Models\Member;
use App\Models\Society;
use App\Models\User;
use App\Jobs\SendWhatsappMessage;
use App\Models\Bill;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;


class MembersImport implements ToCollection, WithHeadingRow
{

    public $society_id; 
    /**
    * @param Collection $collection
    */
    public function __construct($society_id)
    {
        $this->society_id = $society_id;
    }    

    protected $errors = [];

    
    public function chunkSize(): int
    {
        return 100; // Adjust based on memory capacity
    }

    public function collection(Collection $rows)
    {
        $society = Society::find($this->society_id);
        if (!$society) {
            return;
        }
        // dd();`
        
        // Process rows in chunks
        $rows->chunk($this->chunkSize())->each(function ($chunk) use ($society) {
            DB::beginTransaction();
            try {
                foreach ($chunk as $row) {
                    // Handle User creation or fetching
                    $user = User::where('phone', trim($row['mobile']))->first();
                    if (!$user) {
                        $user = User::create([
                            'name'     => trim($row['name_of_resident']),
                            'email'    => trim($row['e_mail']) ?? null,
                            'phone'    => trim($row['mobile']),
                            'role'     => trim($row['role']),
                            'status'   => 'active',
                            'password' => '123456',
                        ]);
                    }

                    // Find Block
                    $block = Block::where('society_id', $society->id)
                        ->where('name', trim($row['block']))
                        ->where('property_number', trim($row['property_number']))
                        ->first();

                    if (!$block) {
                        continue;
                    }

                    // Skip if member already exists
                    if (Member::where('block_id', $block->id)->exists()) {
                        continue;
                    }

                    $due_date = Carbon::createFromFormat('d/m/Y', trim($row['due_date']))->format('Y-m-d');

                    // Create Member
                    $member = new Member();
                    $member->name = trim($row['name_of_resident']);
                    $member->role = trim($row['role']);
                    $member->phone = trim($row['mobile']);
                    $member->email = trim($row['e_mail']) ?? '';
                    $member->user_id = $user->id;
                    $member->society_id = $society->id;
                    $member->block_id = $block->id;
                    $member->floor_number = $block->floor;
                    $member->unit_type = $block->unit_type;
                    $member->aprt_no = $block->property_number;
                    $member->ownership_type = trim($row['ownership']);
                    $member->maintenance_bill = trim($row['due']);
                    $member->maintenance_bill_due_date = $due_date ?? null;
                    $member->save();

                    // Create or update Bill
                    if ($member->save()) {

                        Log::info('Inserted member', $member->toArray());

                        _dLog(eventType: 'info', activityName: 'Bill Creation Started', description: 'Starting the process of creating a new bill');

                        $isBillExist = Bill::where('society_id', $society->id)
                            ->where('member_id', $member->id)
                            ->where('user_id', $user->id)
                            ->whereMonth('due_date', date('m', strtotime($due_date)))
                            ->whereNull('deleted_at')
                            ->first();

                        if ($isBillExist) {
                            $isBillExist->update([
                                'amount' => trim($row['due']),
                                'due_date' => $due_date,
                            ]);
                            $bill = $isBillExist;
                        } else {
                            $bill = Bill::create([
                                'user_id'     => $user->id,
                                'amount'      => trim($row['due']),
                                'due_date'    => $due_date,
                                'status'      => 'unpaid',
                                'created_by'  => auth()->id(),
                                'service_id'  => 2, // Assuming 2 is Maintenance Service
                                'society_id'  => $society->id,
                                'member_id'   => $member->id,
                                'created_at'  => now(),
                                'updated_at'  => now(),
                            ]);
                        }

                        $this->sendBillNotification((object) $bill);

                        $canSend = canSendMessage('whatsapp_message');
                        if ($canSend['status']) {
                            SendWhatsappMessage::dispatch($bill, 'new_bill_create');
                        }
                        // dd($bill);         

                    }

                    // Assign role if admin
                    if ($user->role == 'admin') {
                        $user->assignRole('admin');
                        $adminRole = Role::findByName('admin');
                        $user->syncPermissions($adminRole->permissions);
                    }

                    // Save notification settings
                    $insertDefaultNotification = [];
                    $residentNotifications = config('notification_settings.resident_app');
                    foreach ($residentNotifications as $setting) {
                        $insertDefaultNotification[] = [
                            'name'           => $setting,
                            'status'         => 'enabled',
                            'user_of_system' => 'app',
                            'user_id'        => $user->id,
                            'role'           => $user->role,
                            'society_id'     => $society->id
                        ];
                    }

                    if (!empty($insertDefaultNotification)) {
                        DB::table('notification_settings')->insert($insertDefaultNotification);
                    }

                    if ($user->role == 'admin') {
                        $panelNotifications = config('notification_settings.admin_panel');
                        $panelData = [];
                        foreach ($panelNotifications as $setting) {
                            $panelData[] = [
                                'name'           => $setting,
                                'status'         => 'enabled',
                                'user_of_system' => 'panel',
                                'user_id'        => $user->id,
                                'role'           => $user->role,
                                'society_id'     => $society->id
                            ];
                        }
                        if (!empty($panelData)) {
                            DB::table('notification_settings')->insert($panelData);
                        }
                    }
                }

                DB::commit();
            } catch (\Exception $e) {
                // DB::rollBack();
                Log::error('Import Error: ' . $e->getMessage());
                $this->errors[] = 'Chunk error: ' . $e->getMessage();
            }
        });
    }
    private function sendBillNotification($bill) {
        $checkSett = 'bill_notifications';

        $user = User::whereHas('notificationSettings', function ($query) use ($checkSett, $bill) {
            $query->where('name', $checkSett)
                ->where('user_id', $bill->user_id)
                ->where('status', 'enabled')
                ->where('user_of_system', 'app')
                ->where('society_id', $bill->society_id);
        })->select('id', 'device_id')->first();

        if ($user && $user->device_id) {
            $deviceId = $user->device_id;
            $notificationMessageArray = [
                'title' => 'New Bill Added',
                'body' => "A bill of ₹ " . $bill->amount . " has been added",
            ];

            sendAppPushNotification($user->id, $deviceId, $notificationMessageArray);
        }
    }
    public function getErrors()
    {
        return $this->errors;
    }
}
