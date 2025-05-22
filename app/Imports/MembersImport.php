<?php

namespace App\Imports;

use App\Models\Block;
use App\Models\Member;
use App\Models\Society;
use App\Models\User;
use Exception;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\DB;
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

        $ARRAY = [];
        foreach ($rows as $row) {
            $ARRAY[] = $row['property_number'];
            
            try {
                if (User::where('email', $row['e_mail'])->exists()) {    
                    continue;
                }
               
                $block = Block::where('society_id', $society->id)
                    ->where('name', $row['block'])
                    ->where('property_number', $row['property_number'])
                    ->first();
                if (!$block) {
                    continue;
                }
                $memberExists = Member::where('block_id', $block->id)->exists();
                if ($memberExists) {
                    continue;
                }
                $user = User::create([
                    'name'     => $row['name_of_resident'],
                    'email'    => $row['e_mail'],
                    'phone'    => $row['mobile'],
                    'role'     => $row['role'],
                    'status'   => 'active',
                    'password' => bcrypt('12345678'),
                ]);
                superAdminLog('info', 'start::user created');
                Member::create([
                    'name'      => $row['name_of_resident'],
                    'role'      => $row['role'],
                    'phone'     => $row['mobile'],
                    'email'     => $row['e_mail'],
                    'user_id'   => $user->id,
                    'society_id'=> $society->id,
                    'block_id'  => $block->id,
                    'floor_number' => $block->floor,
                    'unit_type' => $block->unit_type,
                    'aprt_no'   => $block->property_number,
                    'ownership_type' => $row['ownership_status'],
                    'maintenance_bill' => $row['due'],                     
                    // Add more member fields from $row if needed
                ]);

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
                DB::rollBack();
                $this->errors[] = 'Error in row: ' . json_encode($row) . ' - ' . $e->getMessage();
            }

        }
        return redirect()->back()->with([
            'status' => 'success',
            'message' => 'Added successfully'
        ]);
    }

    public function getErrors()
    {
        return $this->errors;
    }
}
