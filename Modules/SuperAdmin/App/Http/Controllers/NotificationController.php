<?php

namespace Modules\SuperAdmin\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\NotificationSettings;
use Illuminate\Http\Request;
use App\Models\Notice;
use App\Models\Society;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Exception;
use Spatie\Permission\Models\Permission;

class NotificationController extends Controller
{
    public function getNotifications(Request $request)
    {
        try {
            $offset = (int) $request->input('offset', 0);
            $limit = (int) $request->input('limit', 10);
            // for load more btn get next pagination noticationcount
            $nextOffset = $offset + 2;

            // Fetch notifications with offset and limit
            $notificationsNext = auth()->user()->notifications()
                ->orderBy('created_at', 'desc')
                ->skip($nextOffset)
                ->take($limit)
                ->get();
            // dd($notifications);

            $filteredNotificationsNext = $notificationsNext->filter(function ($notification) {
                return isset($notification->data['model']);
            });
            $processedNotificationsNext = $filteredNotificationsNext
                ->filter(function ($notification) {
                    $data = $notification->data;

                    // Check if the link would be empty
                    if (isset($data['model']) && isset($data['model_id'])) {
                        $link = generateNotificationLink($data, 'superadmin');
                        return !empty($link); // Keep only notifications with a non-empty link
                    }

                    return false; // Exclude notifications without model or model_id
                })
                ->map(function ($notification) {
                    $data = $notification->data;

                    // Generate the link since it passed the filter
                    $data['link'] = generateNotificationLink($data, 'superadmin');

                    if (isset($data['society_id'])) {
                        $society = Society::find($data['society_id']);
                        $data['society_name'] = $society->name;
                    } else {
                        $data['society_name'] = '';
                    }

                    // Assign the processed data back to the notification
                    $notification->data = $data;

                    return $notification;
                });
            $totalCountNext = $processedNotificationsNext->count();
            //===========current page data
            // Fetch notifications with offset and limit
            $notifications = auth()->user()->notifications()
                ->orderBy('created_at', 'desc')
                ->skip($offset)
                ->take($limit)
                ->get();
            // dd($notifications);

            $filteredNotifications = $notifications->filter(function ($notification) {
                return isset($notification->data['model']);
            });

            $processedNotifications = $filteredNotifications
                ->filter(function ($notification) use ($totalCountNext) {
                    $data = $notification->data;

                    // Check if the link would be empty
                    if (isset($data['model']) && isset($data['model_id'])) {
                        $link = generateNotificationLink($data, 'superadmin');
                        return !empty($link); // Keep only notifications with a non-empty link
                    }

                    return false; // Exclude notifications without model or model_id
                })
                ->map(function ($notification) use ($totalCountNext) {
                    $data = $notification->data;

                    // Generate the link since it passed the filter
                    $data['link'] = generateNotificationLink($data, 'superadmin');

                    if (isset($data['society_id'])) {
                        $society = Society::find($data['society_id']);
                        $data['society_name'] = $society->name;
                    } else {
                        $data['society_name'] = '';
                    }
                    $data['totalCountNext'] = $totalCountNext;

                    // Assign the processed data back to the notification
                    $notification->data = $data;

                    return $notification;
                });



            return response()->json($processedNotifications);
        } catch (Exception $e) {
            // Handle any errors
            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch notifications: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }



    public function markAsRead(Request $request)
    {
        $notification = auth()->user()->unreadNotifications->where('id', $request->id)->first();
        if ($notification) {
            $notification->markAsRead();
        }

        return response()->json(['message' => 'Notification marked as read']);
    }

    public function markAllAsRead()
    {
        try {
            // Get the authenticated user
            $user = auth()->user();

            // Mark all unread notifications as read for the authenticated user
            $user->unreadNotifications->markAsRead();

            // Return success response
            return response()->json(['message' => 'All notifications marked as read']);
        } catch (Exception $e) {
            // Handle any exceptions and return error response
            return response()->json(['message' => 'Error marking notifications as read', 'error' => $e->getMessage()], 500);
        }
    }

    public function updateNotificationSettings(Request $request)
    {
        $request->validate([
            'name' => 'required|string|exists:notification_settings,name',
            'status' => 'required|string|in:enabled,disabled',
        ]);

        // Find the setting and update the status
        $setting = NotificationSettings::where('user_id', auth()->id())
            ->where('user_of_system', 'panel')
            ->where('name', $request->name)
            ->first();

        if ($setting) {
            $setting->status = $request->status;
            $setting->save();

            return response()->json(['status' => 'success', 'message' => 'Notification setting updated successfully.']);
        }

        return response()->json(['status' => 'error', 'message' => 'Notification setting not found.'], 404);
    }



}
