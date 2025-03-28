<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\NotificationSettings;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    //
    public function notificationSettings(Request $request)
    {
        try {
            $user_id = auth()->user()->id;
            // Start building the query
            $notification_settings = NotificationSettings::where('user_id', $user_id)->get();

            // Check if notices are found
            if ($notification_settings->isEmpty()) {
                return res(
                    status: false,
                    message: "No settings found!",
                    code: HTTP_OK
                );
            }

            // Return the response in JSON format
            $data = [
                'notification_settings' => $notification_settings,
            ];
            return res(
                status: true,
                message: "Settings retrived successfully",
                data: $data,
                code: HTTP_OK
            );

        } catch (\Exception $e) {
            // Handle any exceptions that occur
            return res(
                status: false,
                message: $e->getMessage(),
                code: HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function updateNotificationSettings(Request $request)
    {
        \DB::beginTransaction();
        try {
            // Validate input
            $validator = validator($request->all(), [
                'name' => 'required|string',
                'status' => 'required|in:enabled,disabled',
            ]);

            if ($validator->fails()) {
                return res(
                    status: false,
                    message: $validator->errors()->first(),
                    code: HTTP_UNPROCESSABLE_ENTITY
                );
            }

            $user_id = auth()->user()->id;

            $notificationSetting = \DB::table('notification_settings')->where('user_id', $user_id)->where('name', $request->input('name'))->first();

            if (!$notificationSetting) {
                return res(
                    status: false,
                    message: "Notification setting not found!",
                    code: HTTP_OK
                );
            }
            \DB::table('notification_settings')
                ->where('user_id', $user_id)
                ->where('name', $request->input('name'))
                ->update($request->only(['status']));

            \DB::commit();

            $notificationSetting = NotificationSettings::where('user_id', $user_id)->where('name', $request->input('name'))->first();

            $data = [
                'notification_setting' => $notificationSetting,
            ];

            return res(
                status: true,
                message: "Successfully updated",
                data: $data,
                code: HTTP_OK
            );
        } catch (\Exception $e) {
            \DB::rollBack();
            // Handle any exceptions that occur
            return res(
                status: false,
                message: $e->getMessage(),
                code: HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
