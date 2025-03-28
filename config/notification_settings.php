<?php

return [
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
    'default_staff_app' => [
        'new_notice_notifications',
        'new_event_notifications',
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

/**
 * Use those in controllers like below
 */
// $residentNotifications = config('notification_settings.resident_app');
// $adminNotifications = config('notification_settings.admin_panel');
