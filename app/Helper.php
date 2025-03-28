<?php
use Hamcrest\Number\IsCloseTo;
use Illuminate\Support\Facades\Log;
use App\Models\Society;
use App\Models\ActivityLog;
// for notification----------------
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Contract\Messaging;
use App\Models\User;
use App\Notifications\NoticeAlertNotification;

// HTTP Status Codes as Constants
if (!defined('HTTP_CONTINUE'))
    define('HTTP_CONTINUE', 100);
if (!defined('HTTP_SWITCHING_PROTOCOLS'))
    define('HTTP_SWITCHING_PROTOCOLS', 101);
if (!defined('HTTP_PROCESSING'))
    define('HTTP_PROCESSING', 102); // WebDAV

if (!defined('HTTP_OK'))
    define('HTTP_OK', 200);
if (!defined('HTTP_CREATED'))
    define('HTTP_CREATED', 201);
if (!defined('HTTP_ACCEPTED'))
    define('HTTP_ACCEPTED', 202);
if (!defined('HTTP_NON_AUTHORITATIVE_INFORMATION'))
    define('HTTP_NON_AUTHORITATIVE_INFORMATION', 203);
if (!defined('HTTP_NO_CONTENT'))
    define('HTTP_NO_CONTENT', 204);
if (!defined('HTTP_RESET_CONTENT'))
    define('HTTP_RESET_CONTENT', 205);
if (!defined('HTTP_PARTIAL_CONTENT'))
    define('HTTP_PARTIAL_CONTENT', 206);
if (!defined('HTTP_MULTI_STATUS'))
    define('HTTP_MULTI_STATUS', 207); // WebDAV
if (!defined('HTTP_ALREADY_REPORTED'))
    define('HTTP_ALREADY_REPORTED', 208); // WebDAV
if (!defined('HTTP_IM_USED'))
    define('HTTP_IM_USED', 226);

if (!defined('HTTP_MULTIPLE_CHOICES'))
    define('HTTP_MULTIPLE_CHOICES', 300);
if (!defined('HTTP_MOVED_PERMANENTLY'))
    define('HTTP_MOVED_PERMANENTLY', 301);
if (!defined('HTTP_FOUND'))
    define('HTTP_FOUND', 302); // Previously "Moved Temporarily"
if (!defined('HTTP_SEE_OTHER'))
    define('HTTP_SEE_OTHER', 303);
if (!defined('HTTP_NOT_MODIFIED'))
    define('HTTP_NOT_MODIFIED', 304);
if (!defined('HTTP_USE_PROXY'))
    define('HTTP_USE_PROXY', 305); // Deprecated
if (!defined('HTTP_TEMPORARY_REDIRECT'))
    define('HTTP_TEMPORARY_REDIRECT', 307);
if (!defined('HTTP_PERMANENT_REDIRECT'))
    define('HTTP_PERMANENT_REDIRECT', 308);

if (!defined('HTTP_BAD_REQUEST'))
    define('HTTP_BAD_REQUEST', 400);
if (!defined('HTTP_UNAUTHORIZED'))
    define('HTTP_UNAUTHORIZED', 401);
if (!defined('HTTP_PAYMENT_REQUIRED'))
    define('HTTP_PAYMENT_REQUIRED', 402);
if (!defined('HTTP_FORBIDDEN'))
    define('HTTP_FORBIDDEN', 403);
if (!defined('HTTP_NOT_FOUND'))
    define('HTTP_NOT_FOUND', 404);
if (!defined('HTTP_METHOD_NOT_ALLOWED'))
    define('HTTP_METHOD_NOT_ALLOWED', 405);
if (!defined('HTTP_NOT_ACCEPTABLE'))
    define('HTTP_NOT_ACCEPTABLE', 406);
if (!defined('HTTP_PROXY_AUTHENTICATION_REQUIRED'))
    define('HTTP_PROXY_AUTHENTICATION_REQUIRED', 407);
if (!defined('HTTP_REQUEST_TIMEOUT'))
    define('HTTP_REQUEST_TIMEOUT', 408);
if (!defined('HTTP_CONFLICT'))
    define('HTTP_CONFLICT', 409);
if (!defined('HTTP_GONE'))
    define('HTTP_GONE', 410);
if (!defined('HTTP_LENGTH_REQUIRED'))
    define('HTTP_LENGTH_REQUIRED', 411);
if (!defined('HTTP_PRECONDITION_FAILED'))
    define('HTTP_PRECONDITION_FAILED', 412);
if (!defined('HTTP_PAYLOAD_TOO_LARGE'))
    define('HTTP_PAYLOAD_TOO_LARGE', 413);
if (!defined('HTTP_URI_TOO_LONG'))
    define('HTTP_URI_TOO_LONG', 414);
if (!defined('HTTP_UNSUPPORTED_MEDIA_TYPE'))
    define('HTTP_UNSUPPORTED_MEDIA_TYPE', 415);
if (!defined('HTTP_RANGE_NOT_SATISFIABLE'))
    define('HTTP_RANGE_NOT_SATISFIABLE', 416);
if (!defined('HTTP_EXPECTATION_FAILED'))
    define('HTTP_EXPECTATION_FAILED', 417);
if (!defined('HTTP_I_AM_A_TEAPOT'))
    define('HTTP_I_AM_A_TEAPOT', 418); // Joke RFC 2324
if (!defined('HTTP_MISDIRECTED_REQUEST'))
    define('HTTP_MISDIRECTED_REQUEST', 421);
if (!defined('HTTP_UNPROCESSABLE_ENTITY'))
    define('HTTP_UNPROCESSABLE_ENTITY', 422); // WebDAV
if (!defined('HTTP_LOCKED'))
    define('HTTP_LOCKED', 423); // WebDAV
if (!defined('HTTP_FAILED_DEPENDENCY'))
    define('HTTP_FAILED_DEPENDENCY', 424); // WebDAV
if (!defined('HTTP_TOO_EARLY'))
    define('HTTP_TOO_EARLY', 425);
if (!defined('HTTP_UPGRADE_REQUIRED'))
    define('HTTP_UPGRADE_REQUIRED', 426);
if (!defined('HTTP_PRECONDITION_REQUIRED'))
    define('HTTP_PRECONDITION_REQUIRED', 428);
if (!defined('HTTP_TOO_MANY_REQUESTS'))
    define('HTTP_TOO_MANY_REQUESTS', 429);
if (!defined('HTTP_REQUEST_HEADER_FIELDS_TOO_LARGE'))
    define('HTTP_REQUEST_HEADER_FIELDS_TOO_LARGE', 431);
if (!defined('HTTP_UNAVAILABLE_FOR_LEGAL_REASONS'))
    define('HTTP_UNAVAILABLE_FOR_LEGAL_REASONS', 451);

if (!defined('HTTP_INTERNAL_SERVER_ERROR'))
    define('HTTP_INTERNAL_SERVER_ERROR', 500);
if (!defined('HTTP_NOT_IMPLEMENTED'))
    define('HTTP_NOT_IMPLEMENTED', 501);
if (!defined('HTTP_BAD_GATEWAY'))
    define('HTTP_BAD_GATEWAY', 502);
if (!defined('HTTP_SERVICE_UNAVAILABLE'))
    define('HTTP_SERVICE_UNAVAILABLE', 503);
if (!defined('HTTP_GATEWAY_TIMEOUT'))
    define('HTTP_GATEWAY_TIMEOUT', 504);
if (!defined('HTTP_HTTP_VERSION_NOT_SUPPORTED'))
    define('HTTP_HTTP_VERSION_NOT_SUPPORTED', 505);
if (!defined('HTTP_VARIANT_ALSO_NEGOTIATES'))
    define('HTTP_VARIANT_ALSO_NEGOTIATES', 506);
if (!defined('HTTP_INSUFFICIENT_STORAGE'))
    define('HTTP_INSUFFICIENT_STORAGE', 507); // WebDAV
if (!defined('HTTP_LOOP_DETECTED'))
    define('HTTP_LOOP_DETECTED', 508); // WebDAV
if (!defined('HTTP_NOT_EXTENDED'))
    define('HTTP_NOT_EXTENDED', 510);
if (!defined('HTTP_NETWORK_AUTHENTICATION_REQUIRED'))
    define('HTTP_NETWORK_AUTHENTICATION_REQUIRED', 511);

if (!function_exists('parseStatus')) {

    function parseStatus($activeInactive, $returnStatusNumOrSelected)
    {
        /**
         * $returnStatusNumOrSelected == 0, return 0 or 1
         * $returnStatusNumOrSelected == 1, return selected for active status
         * $returnStatusNumOrSelected == 2, return selected for inactive status
         */
        $activeSelected = '';
        $inactiveSelected = '';
        if ($activeInactive == 'active') {
            $status = 1;
            $activeSelected = 'selected';
        } else {
            $status = 0;
            $inactiveSelected = 'selected';
        }

        if ($returnStatusNumOrSelected == 0) {
            return $status;
        } elseif ($returnStatusNumOrSelected == 1) {
            return $activeSelected;
        } elseif ($returnStatusNumOrSelected == 2) {
            return $inactiveSelected;
        }
    }
}

/**
 * Reusable response method for JSON output.
 *
 * @param bool $status
 * @param string $message
 * @param int|null $code
 * @param array|null $header
 * @return \Illuminate\Http\JsonResponse
 */
if (!function_exists('res')) {

    function res($status, $message = null, $data = null, $code = null, $header = null)
    {
        // Normalize the status and message
        $code = $code ?? 200; // Default to 200 OK if no code provided
        $response = [
            'status' => $status,
            'message' => $message,
            'data' => $data,
        ];

        // Determine log level based on the status code
        if ($code >= 500) {
            // Log critical error for 500 series (Server errors)
            $logType = 'critical';
            $logMessage = "Critical Server Error";
        } elseif ($code >= 400) {
            // Log error for 400 series (Client errors)
            $logType = 'error';
            $logMessage = "Client Error";
        } elseif ($code >= 300) {
            // Log warning for 300 series (Redirects)
            $logType = 'warning';
            $logMessage = "Redirection";
        } else {
            // Log info for successful (200 series) responses
            $logType = 'info';
            $logMessage = "Successful Request";
        }

        // Logging the request details with the appropriate level in the 'monthly' channel
        Log::channel('apilog')->{$logType}('API Log - ' . $logMessage, [
            'status' => $status,
            'message' => $message,
            'code' => $code,
            'timestamp' => now(),
            'request_data' => request()->all(), // Log the request data
            'url' => request()->url(), // Log the URL where the error occurred
            'method' => request()->method(), // HTTP method
            'headers' => request()->header(), // Headers for deeper debugging
            'ip' => request()->ip(), // Log the client IP
        ]);

        // 500 error handling when APP_DEBUG is false
        if ($status == 500 && env('APP_DEBUG') == false) {
            $message = "An unexpected error occurred. Please try again later."; // Custom message for production
        }

        // Return the JSON response
        return response()->json($response, $code, $header ?? []);
    }
}

/**
 * Log a custom entry to the superAdminlog channel
 *
 * @param string $logType Type of log (info, warning, error, etc.)
 * @param string $logMessage Log message
 * @param bool $status Log status
 * @param string|null $message Optional message
 * @param int|null $code Optional HTTP or custom code
 */

if (!function_exists('superAdminLog')) {
    function superAdminLog($logType, $logMessage, $message = null)
    {
        if (Auth::check() && auth()->user()->role == "admin") {

            \Log::channel('adminlog')->{$logType}('Web Log - ' . $logMessage, [
                'message' => $message,
                'timestamp' => now(),
                'request_data' => request()->all(), // Log the request data
                'url' => request()->url(), // Log the URL where the error occurred
                'method' => request()->method(), // HTTP method
                'headers' => request()->header(), // Headers for deeper debugging
                'ip' => request()->ip(), // Log the client IP
            ]);
        } elseif (Auth::check() && auth()->user()->role == "super_admin") {

            \Log::channel('superAdminlog')->{$logType}('Web Log - ' . $logMessage, [
                'message' => $message,
                'timestamp' => now(),
                'request_data' => request()->all(), // Log the request data
                'url' => request()->url(), // Log the URL where the error occurred
                'method' => request()->method(), // HTTP method
                'headers' => request()->header(), // Headers for deeper debugging
                'ip' => request()->ip(), // Log the client IP
            ]);
        }
    }
}

if (!function_exists('_fLog')) {
    function _fLog($dLogId, $logType, $logMessage, $message = null)
    {
        if (Auth::check() && auth()->user()->role == "admin") {

            \Log::channel('adminlog')->{$logType}('Web Log - ' . $logMessage, [
                'message' => $message,
                'timestamp' => now(),
                'request_data' => request()->all(), // Log the request data
                'url' => request()->url(), // Log the URL where the error occurred
                'method' => request()->method(), // HTTP method
                'headers' => request()->header(), // Headers for deeper debugging
                'ip' => request()->ip(), // Log the client IP
                'dLogId' => $dLogId, // dLogId
            ]);
        } elseif (Auth::check() && auth()->user()->role == "super_admin") {

            \Log::channel('superAdminlog')->{$logType}('Web Log - ' . $logMessage, [
                'message' => $message,
                'timestamp' => now(),
                'request_data' => request()->all(), // Log the request data
                'url' => request()->url(), // Log the URL where the error occurred
                'method' => request()->method(), // HTTP method
                'headers' => request()->header(), // Headers for deeper debugging
                'ip' => request()->ip(), // Log the client IP
                'dLogId' => $dLogId, // dLogId
            ]);
        }
    }
}

/**
 * Retrieve the currently selected society or set a default if none is selected.
 *
 * This function checks if a selected society is stored in the session. If not,
 * it retrieves the first available society from the database and stores its ID in the session.
 * If no society exists, it redirects the user to the settings page, prompting them
 * to create a society.
 *
 * @param \Illuminate\Http\Request $request The HTTP request object.
 *
 * @return \App\Models\Society|\Illuminate\Http\RedirectResponse
 *         Returns the selected society if found, or redirects the user to the settings
 *         page if no society exists.
 */
if (!function_exists('getSelectedSociety')) {
    function getSelectedSociety($request)
    {
        // Check if a society is already selected and stored in the session
        $selectedSociety = session('__selected_society__');

        if (Auth::check() && auth()->user()->role == "admin") {
            $selectedSociety = auth()->user()->member->society_id;
            session(['__selected_society__' => $selectedSociety]);
            return $selectedSociety;
        }

        if (!$selectedSociety) {
            // If no society is selected, fetch the first available one from the database
            $selectedSocietyInfo = Society::where('status', 'active')->orderBy('id', 'asc')->first();
            if (!$selectedSocietyInfo) {
                session(['active_tab' => '#tab3-tab']); // Set the active tab to the society creation tab
                return redirect()->route('superadmin.settings')->with([
                    'status' => 'warning',
                    'message' => 'Please create a Society First !'
                ]);
            }
            $selectedSociety = $selectedSocietyInfo->id;

            // Store the selected society's ID in the session for future use
            if (isset($request->society_id) && !empty($request->society_id)) {
                session(['__selected_society__' => $request->society_id]);
            } else {
                session(['__selected_society__' => $selectedSocietyInfo->id]);
            }
        }

        // Return the selected society object
        return $selectedSociety;
    }
}


if (!function_exists('_dLog')) {
    function _dLog($eventType, $activityName, $description, $modelType = null, $modelId = null, $status = 'success', $severityLevel = 0, $beforeData = null, $afterData = null, $requestData = null)
    {
        try {
            if (isset(auth()->user()->role) && auth()->user()->role == 'admin') {
                $get_society_id = auth()->user()->member->society_id;
            } elseif (isset(auth()->user()->role) && auth()->user()->role == 'super_admin') {
                $get_society_id = session('__selected_society__');
            }
            if (!isset($get_society_id)) {
                $get_society_id = 0;
            }
            $activityLog = ActivityLog::create([
                'event_type' => $eventType,
                'activity_name' => $activityName,
                'description' => $description,
                'user_id' => auth()->id(),
                'user_role' => auth()->user() ? auth()->user()->role : null,
                'society_id' => $get_society_id,
                'before_data' => $beforeData ? serialize($beforeData) : null,
                'after_data' => $afterData ? serialize($afterData) : null,
                'request_data' => $requestData ? serialize($requestData) : null,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'route_name' => request()->route()->getName(),
                'model_type' => $modelType,
                'model_id' => $modelId,
                'status' => $status,
                'severity_level' => $severityLevel,
            ]);
            $insertId = $activityLog->id;
            _fLog($insertId, $eventType, $activityName, $description);
            return $insertId;
        } catch (Exception $e) {
            _fLog(dLogId: 0, logType: 'error', logMessage: 'Exception::', message: $e->getMessage());
            \Log::error('Failed to create activity log: ' . $e->getMessage());
            return 0;
        }
    }
}

if (!function_exists('hasPermissionLike')) {
    function hasPermissionLike(string $prefix): bool
    {
        $user = auth()->user();

        // Ensure the user is authenticated
        if (!$user) {
            return false;
        }

        // Fetch all permissions directly from Spatie's model
        $permissions = $user->getAllPermissions()->pluck('name');

        // Check if any permission starts with the specified prefix
        return $permissions->contains(function ($permission) use ($prefix) {
            return Str::startsWith($permission, $prefix);
        });
    }
}


/**
 * Send push notification to the app users.
 *
 * @param string $deviceId
 * @param string $notificationMessage
 * @param array|null $notificationData
 * @param object|null $othersInfoObj
 */

if (!function_exists('sendAppPushNotification')) {
    function sendAppPushNotification($userId, $deviceId, $notificationMessageArray, $notificationDataArray = null, $othersInfoObj = null)
    {
        // Ensure title and body are present before proceeding
        if (empty($notificationMessageArray['title']) || empty($notificationMessageArray['body'])) {
            \Log::warning("Notification skipped: Missing title or body", [
                'userId' => $userId,
                'deviceId' => $deviceId,
                'notificationMessageArray' => $notificationMessageArray
            ]);
            return true; // Stop execution
        }
        // =====================================
        // in-app notification
        // =====================================
        $user = User::find($userId);

        // Separate try-catch for database storage
        if ($user) {

            \Log::info('@@push-notification@@ ::: To User Mobile :::', ['phone' => $user->phone]);

            try {
                $notificationId = \Str::uuid()->toString();

                $user->notifications()->create([
                    'id' => $notificationId,
                    'type' => 'pushNotification',
                    'data' => [
                        'title' => $notificationMessageArray['title'],
                        'body' => $notificationMessageArray['body'],
                        'data' => $notificationDataArray ?? [],
                    ],
                ]);

                \Log::info('in-app-notification <created>: ', ['id' => $notificationId]);
            } catch (\Throwable $throwable) {
                \Log::error('in-app-notification <exception|error>: ', [
                    'message' => $throwable->getMessage(),
                    'file' => $throwable->getFile(),
                    'line' => $throwable->getLine(),
                    'trace' => $throwable->getTraceAsString(),
                ]);
            }
        }
        // =====================================
        // push notification
        // =====================================
        if (!empty($deviceId)) {

            try {
                $firebaseMessaging = app(Messaging::class);

                $notificationMessage = (object) $notificationMessageArray;

                $messageData = [
                    'token' => $deviceId,
                    'notification' => [
                        'title' => $notificationMessage->title,
                        'body' => $notificationMessage->body
                    ],
                    'data' => $notificationDataArray ?? [],

                    // Setting High Priority for Android
                    'android' => [
                        'priority' => 'high',
                        'notification' => [
                            'channel_id' => 'ghp_high_priority_channel', // Updated to match Flutter
                            'sound' => 'default',
                        ],
                    ],
                    // Global Priority Setting for FCM
                    'priority' => 'high', // Ensures FCM delivers the notification ASAP
                ];

                if (!empty($notificationDataArray)) {
                    $messageData['android']['ttl'] = 0;
                }

                \Log::info(':::::::::::::::::::::');
                \Log::info('firebase-payload:', $messageData);
                // Create the CloudMessage object
                $message = CloudMessage::fromArray($messageData);

                // Send the message to Firebase
                $response = $firebaseMessaging->send($message);

                //to store on database
                \Log::info('firebase-response<success>: ', ['response' => $response]);

            } catch (\Kreait\Firebase\Exception\MessagingException $e) {
                $errorCode = $e->getCode(); // Get HTTP status code
                $errorMessage = json_encode($e->getMessage()); // Get the error message
                $errorDetails = json_encode($e->getTrace(), JSON_PRETTY_PRINT); // Full exception trace

                \Log::error("firebase-response<error>: Code: $errorCode, Message: $errorMessage, Trace: $errorDetails");
            } catch (\Throwable $throwable) {
                \Log::error('firebase-response<exception>: ' . json_encode([
                    'code' => $throwable->getCode(),
                    'message' => $throwable->getMessage(),
                    'file' => $throwable->getFile(),
                    'line' => $throwable->getLine(),
                    'trace' => $throwable->getTrace()
                ], JSON_PRETTY_PRINT));
            }
        }
    }
}

/**
 * Send push notification to the admin panel users.
 *
 * @param string $notificationMessage
 * @param array|null $notificationData
 * @param object|null $othersInfoObj
 */

// if (!function_exists('sendPanelNotification')) {
//     function sendPanelNotification($notificationMessage, $notificationData = null, $othersInfoObj = null)
//     {
//         try {
//             // Get all users who are admins and have enabled notifications for the panel
//             $adminUsers = User::whereHas('notificationSettings', function ($query) {
//                 $query->where('name', 'push_notifications')
//                     ->where('status', 'enabled')
//                     ->where('user_of_system', 'panel');
//             })->get();

//             // Loop through the admin users and send the notification
//             foreach ($adminUsers as $user) {
//                 // You can customize this to use a specific notification class
//                 $user->notify(new NoticeAlertNotification($notificationMessage));
//             }

//             \Log::info('Panel notification sent to all admins.');
//         } catch (\Exception $e) {
//             \Log::error('Error sending panel notification: ' . $e->getMessage());
//         }
//     }
// }


if (!function_exists('generateNotificationLink')) {
    /**
     * Generate a notification link based on the given data.
     *
     * @param array $notificationData
     * @param string $module
     * @return string
     */
    function generateNotificationLink(array $notificationData, string $module): string
    {
        // Check if 'model' key exists in the data
        if (!array_key_exists('model', $notificationData)) {
            return '';
        }

        // Extract data
        $model = $notificationData['model'] ?? null;
        $modelId = $notificationData['model_id'] ?? null;

        if (!empty($notificationData['society_id'])) {
            $society_id = $notificationData['society_id'];
        } else {
            $society_id = session('__selected_society__');
        }

        // Validate the model and model ID
        if ($model && $modelId) {
            // Check if the model class exists
            $modelClass = "App\\Models\\$model"; // Adjust the namespace if needed
            if (class_exists($modelClass)) {
                // Check if the model ID exists in the database
                $modelInstance = $modelClass::find($modelId);
                if (!$modelInstance) {
                    return ''; // Return empty if model ID does not exist
                }
            } else {
                return ''; // Return empty if model class does not exist
            }
        } else {
            return ''; // Return empty if model or model ID is not valid
        }

        // Handle model-specific links using a switch case
        if ($module == 'superadmin') {
            switch ($model) {
                case 'Event':
                    if ($modelId) {
                        return '<a href="javascript:void(0);" class="notificationMoreBtn" data-societyid="' . $society_id . '" data-href="' . route($module . '.event.details', ['id' => $modelId]) . '" id="' . $modelId . '">View Event Details</a>';
                    }
                    break;

                case 'Notice':
                    if ($modelId) {
                        return '<a href="javascript:void(0);" class="notificationMoreBtn" data-societyid="' . $society_id . '" data-href="' . route($module . '.notice.details', ['id' => $modelId]) . '" id="' . $modelId . '">View Notice Details</a>';
                    }
                    break;

                case 'Sos':
                    if ($modelId) {
                        return '<a href="javascript:void(0);" class="notificationMoreBtn" data-societyid="' . $society_id . '" data-href="' . route($module . '.sos.details', ['id' => $modelId]) . '" id="' . $modelId . '"> View SOS Details</a>';
                    }
                    break;

                case 'Poll':
                    if ($modelId) {
                        return '<a href="javascript:void(0);" class="notificationMoreBtn" data-societyid="' . $society_id . '" data-href="' . route($module . '.poll.details', ['id' => $modelId]) . '" id="' . $modelId . '">View Poll Details</a>';
                    }
                    break;

                case 'ReferProperty':
                    if ($modelId) {
                        return '<a href="javascript:void(0);" class="notificationMoreBtn" data-societyid="' . $society_id . '" data-href="' . route($module . '.refer_property.details', ['id' => $modelId]) . '" id="' . $modelId . '">View Refer Details</a>';
                    }
                    break;

                case 'TradeProperty':
                    if ($modelId) {
                        return '<a href="javascript:void(0);" class="notificationMoreBtn" data-societyid="' . $society_id . '" data-href="' . route($module . '.property_listing.details', ['id' => $modelId]) . '" id="' . $modelId . '">View Listing Details</a>';
                    }
                    break;
                case 'Complaint':
                    if ($modelId) {
                        return '<a href="javascript:void(0);" class="notificationMoreBtn" data-societyid="' . $society_id . '" data-href="' . route($module . '.complaint.details', ['id' => $modelId]) . '" id="' . $modelId . '">View Complaint Details</a>';
                    }
                    break;
                case 'Document':
                    if ($modelId) {
                        return '<a href="javascript:void(0);" class="notificationMoreBtn" data-societyid="' . $society_id . '" data-href="' . route($module . '.document.details', ['id' => $modelId]) . '" id="' . $modelId . '">View Document Details</a>';
                    }
                    break;
                case 'Parcel':
                    if ($modelId) {
                        return '<a href="javascript:void(0);" class="notificationMoreBtn" data-societyid="' . $society_id . '" data-href="' . route($module . '.parcel.details', ['id' => $modelId]) . '" id="' . $modelId . '">View Parcel Details</a>';
                    }
                    break;

                default:
                    return ''; // Return empty if model is not handled
            }
        } else {
            switch ($model) {
                case 'Event':
                    if ($modelId) {
                        return '<a href="' . route($module . '.event.details', ['id' => $modelId]) . '" id="' . $modelId . '">View Event Details</a>';
                    }
                    break;

                case 'Notice':
                    if ($modelId) {
                        return '<a href="' . route($module . '.notice.details', ['id' => $modelId]) . '" id="' . $modelId . '">View Notice Details</a>';
                    }
                    break;

                case 'Sos':
                    if ($modelId) {
                        return '<a href="' . route($module . '.sos.details', ['id' => $modelId]) . '" id="' . $modelId . '"> View SOS Details</a>';
                    }
                    break;

                case 'Poll':
                    if ($modelId) {
                        return '<a href="' . route($module . '.poll.details', ['id' => $modelId]) . '" id="' . $modelId . '">View Poll Details</a>';
                    }
                    break;

                case 'ReferProperty':
                    if ($modelId) {
                        return '<a href="' . route($module . '.refer_property.details', ['id' => $modelId]) . '" id="' . $modelId . '">View Refer Details</a>';
                    }
                    break;

                case 'TradeProperty':
                    if ($modelId) {
                        return '<a href="' . route($module . '.property_listing.details', ['id' => $modelId]) . '" id="' . $modelId . '">View Listing Details</a>';
                    }
                    break;
                case 'Complaint':
                    if ($modelId) {
                        return '<a href="' . route($module . '.complaint.details', ['id' => $modelId]) . '" id="' . $modelId . '">View Complaint Details</a>';
                    }
                    break;
                case 'Document':
                    if ($modelId) {
                        return '<a href="' . route($module . '.document.details', ['id' => $modelId]) . '" id="' . $modelId . '">View Document Details</a>';
                    }
                    break;
                case 'Parcel':
                    if ($modelId) {
                        return '<a href="' . route($module . '.parcel.details', ['id' => $modelId]) . '" id="' . $modelId . '">View Parcel Details</a>';
                    }
                    break;

                default:
                    return ''; // Return empty if model is not handled
            }
        }


        return ''; // Return empty if model or model_id is not valid
    }
}

if (!function_exists('isNotificationSettingEnabled')) {
    /**
     * Check if a notification setting is enabled for a specific user and device.
     *
     * @param string $settingName The name of the notification setting.
     * @param int $userId The user ID to check for.
     * @param string $device The device type ('app', 'panel').
     * @return mixed The user instance if the setting is enabled, null otherwise.
     */
    function isNotificationSettingEnabled($settingName, $userId, $device)
    {
        return User::whereHas('notificationSettings', function ($query) use ($settingName, $userId, $device) {
            $query->where('name', $settingName)
                ->where('status', 'enabled')
                ->where('user_of_system', $device)
                ->where('user_id', $userId);
        })->first();
    }
}

