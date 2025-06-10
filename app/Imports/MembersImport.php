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


    public function collection(Collection $rows)
    {    
        $society = Society::find($this->society_id);
        if($society == null){
            return response()->json(['error' => 'Society not found'], 404);
        }
        foreach ($rows as $row) {
            try {               
                if (User::where('phone', trim($row['mobile']))->exists()) { 
                    $user = User::where('phone', trim($row['mobile']))->first();
                }else{
                    $user = User::create([
                        'name'     => trim($row['name_of_resident']),
                        'email'    => trim($row['e_mail']),
                        'phone'    => trim($row['mobile']),
                        'role'     => trim($row['role']),
                        'status'   => 'active',
                        'password' => Hash::make('12345678'),
                    ]);
                } 

                $block = Block::where('society_id', $society->id)
                ->where('name', trim($row['block']))
                ->where('property_number', trim($row['property_number']))
                ->first();
                if (!$block) {
                    continue;
                }
                
                $memberExists = Member::where('block_id', $block->id)->exists();
                if ($memberExists) { 
                    continue;
                }
                superAdminLog('info', 'start::user created');    
                $due_date = Carbon::createFromFormat('d/m/Y', trim($row['due_date']))->format('Y-m-d');
                $member = new Member();
                $member->name = trim($row['name_of_resident']);
                $member->role = trim($row['role']);
                $member->phone = trim($row['mobile']);
                $member->email = trim($row['e_mail']);
                $member->user_id = $user->id;
                $member->society_id = $society->id;
                $member->block_id = $block->id;
                $member->floor_number = $block->floor;
                $member->unit_type = $block->unit_type;
                $member->aprt_no = $block->property_number;
                $member->ownership_type = trim($row['ownership']);
                $member->maintenance_bill = trim($row['due']);
                $member->maintenance_bill_due_date = $due_date ?? null;
  
                if($member->save()){
                    Log::info('Inserted member', $member->toArray());

                    _dLog(eventType: 'info', activityName: 'Bill Creation Started', description: 'Starting the process of creating a new bill');
                    DB::beginTransaction();
                    $isBillExist = Bill::where('society_id', $society->id)
                    ->where('member_id',$member->id)
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
                }
                
                if ($user->role == 'admin') {
                    // Assign the 'admin' role to the user
                    $user->assignRole('admin');

                    // Fetch all permissions assigned to the 'admin' role
                    $adminRole = Role::findByName('admin');
                    $adminPermissions = $adminRole->permissions;

                    // Assign all permissions of the 'admin' role directly to the user
                    $user->syncPermissions($adminPermissions);//seeder is Used
                }

                // =================================================
                // save notification defaults values
                // resident app for resident + admin role
                $insertDefaultNotification = [];
                $residentAppNotifications = config('notification_settings.resident_app');
                foreach ($residentAppNotifications as $defaultSettingName) {
                    $insertDefaultNotification[] = [
                        'name' => $defaultSettingName,
                        'status' => 'enabled',
                        'user_of_system' => 'app',
                        'user_id' => $user->id,
                        'role' => $user->role,
                        'society_id' => $society->id
                    ];
                }
                if (!empty($insertDefaultNotification)) {
                    DB::table('notification_settings')->insert($insertDefaultNotification);
                }

                if ($user->role == 'admin') {
                    //for admin panel notification settings
                    $residentAppNotifications = config('notification_settings.admin_panel');
                    $insertDefaultNotificationPanel = [];
                    foreach ($residentAppNotifications as $defaultSettingName) {
                        $insertDefaultNotificationPanel[] = [
                            'name' => $defaultSettingName,
                            'status' => 'enabled',
                            'user_of_system' => 'panel',
                            'user_id' => $user->id,
                            'role' => $user->role,
                            'society_id' => $society->id
                        ];
                    }
                    if (!empty($insertDefaultNotificationPanel)) {
                        DB::table('notification_settings')->insert($insertDefaultNotificationPanel);
                    }
                }
                // =====================================================

                superAdminLog('info', 'start::member created');
                DB::commit();
                superAdminLog('info', 'end::store');

            
            } catch (\Exception $e) {
                superAdminLog('error', 'Exception::', $e->getMessage());
                // DB::rollBack();
                $this->errors[] = 'Error in row: ' . json_encode($row) . ' - ' . $e->getMessage();
            }

        }
        return redirect()->back()->with([
            'status' => 'success',
            'message' => 'Added successfully'
        ]);
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
