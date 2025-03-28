<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class TestingController extends Controller
{

    public function fixUsersSettings()
    {
        try {
            DB::beginTransaction();

            $settingsSet = [
                'resident_app' => [
                    'new_notice_notifications',
                    'new_event_notifications',
                    'complaint_related_notifications',
                    'property_related_notifications',
                    'document_notifications',
                    'poll_notifications',
                    'bill_notifications',
                    'visitor_notifications', // always enabled, disabled is prohibited
                    'parcel_notifications', // always enabled, disabled is prohibited
                ],
                'security_guard_app' => [
                    'new_notice_notifications',
                    'new_event_notifications',
                    'visitor_notifications', // always enabled, disabled is prohibited
                    'parcel_notifications', // always enabled, disabled is prohibited
                ],
                'service_provider_app' => [
                    'assigned_service_notifications',
                    'new_service_notifications',
                ],
                'admin_panel' => [
                    'new_notice_notifications',
                    'new_event_notifications',
                    'sos_notifications', // always enabled, disabled is prohibited
                    'complaint_related_notifications',
                    'property_related_notifications',
                    'refer_property_notifications',
                    'document_notifications',
                    'poll_notifications',
                    'bill_notifications',
                    'parcel_complaint_notifications',
                ],
                'super_admin_panel' => [
                    'new_notice_notifications',
                    'new_event_notifications',
                    'sos_notifications', // always enabled, disabled is prohibited
                    'complaint_related_notifications',
                    'property_related_notifications',
                    'refer_property_notifications',
                    'document_notifications',
                    'poll_notifications',
                    'bill_notifications',
                    'parcel_complaint_notifications',
                ],
            ];

            $users = User::whereIn('role', ['super_admin', 'admin', 'resident', 'staff', 'staff_security_guard'])->get();

            foreach ($users as $user) {
                $insertDefaultNotification = [];
                $societyId = null;

                switch ($user->role) {
                    case 'super_admin':
                        foreach ($settingsSet['super_admin_panel'] as $defaultSettingName) {
                            $exists = DB::table('notification_settings')
                                ->where([
                                    'name' => $defaultSettingName,
                                    'user_id' => $user->id,
                                    'role' => $user->role,
                                    'user_of_system' => 'panel',
                                ])
                                ->exists();

                            if (!$exists) {
                                $insertDefaultNotification[] = [
                                    'name' => $defaultSettingName,
                                    'status' => 'enabled',
                                    'user_of_system' => 'panel',
                                    'user_id' => $user->id,
                                    'role' => $user->role,
                                ];
                            }
                        }
                        break;

                    case 'admin':
                    case 'resident':
                        $societyId = $user->member?->society_id;
                        foreach ($settingsSet['resident_app'] as $defaultSettingName) {
                            $exists = DB::table('notification_settings')
                                ->where([
                                    'name' => $defaultSettingName,
                                    'user_id' => $user->id,
                                    'role' => $user->role,
                                    'user_of_system' => 'app',
                                ])
                                ->exists();

                            if (!$exists) {
                                $insertDefaultNotification[] = [
                                    'name' => $defaultSettingName,
                                    'status' => 'enabled',
                                    'user_of_system' => 'app',
                                    'user_id' => $user->id,
                                    'role' => $user->role,
                                    'society_id' => $societyId,
                                ];
                            }
                        }
                        if ($user->role === 'admin') {
                            foreach ($settingsSet['admin_panel'] as $defaultSettingName) {
                                $exists = DB::table('notification_settings')
                                    ->where([
                                        'name' => $defaultSettingName,
                                        'user_id' => $user->id,
                                        'role' => $user->role,
                                        'user_of_system' => 'panel',
                                    ])
                                    ->exists();

                                if (!$exists) {
                                    $insertDefaultNotification[] = [
                                        'name' => $defaultSettingName,
                                        'status' => 'enabled',
                                        'user_of_system' => 'panel',
                                        'user_id' => $user->id,
                                        'role' => $user->role,
                                        'society_id' => $societyId,
                                    ];
                                }
                            }
                        }
                        break;

                    case 'staff':
                    case 'staff_security_guard':
                        $societyId = $user->staff?->society_id;
                        $appSettings = $user->role === 'staff' ? $settingsSet['service_provider_app'] : $settingsSet['security_guard_app'];
                        foreach ($appSettings as $defaultSettingName) {
                            $exists = DB::table('notification_settings')
                                ->where([
                                    'name' => $defaultSettingName,
                                    'user_id' => $user->id,
                                    'role' => $user->role,
                                    'user_of_system' => 'app',
                                ])
                                ->exists();

                            if (!$exists) {
                                $insertDefaultNotification[] = [
                                    'name' => $defaultSettingName,
                                    'status' => 'enabled',
                                    'user_of_system' => 'app',
                                    'user_id' => $user->id,
                                    'role' => $user->role,
                                    'society_id' => $societyId,
                                ];
                            }
                        }
                        break;
                }

                if (!empty($insertDefaultNotification)) {
                    DB::table('notification_settings')->insert($insertDefaultNotification);
                }
            }

            DB::commit();
            return response()->json(['message' => 'User settings updated successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with([
                'status' => 'error - 500',
                'message' => $e->getMessage(),
            ]);
        }
    }



}
