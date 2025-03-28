<h3>Notification Setting</h3>
<div class="setting-list">
    <ol>
        @if (hasPermissionLike('complaints.'))
            <li>
                <p>Complaints Related Notification</p>
                <label class="switch">
                    <input type="checkbox" data-setting-name="complaint_related_notifications"
                        {{ $notificationSettings->where('name', 'complaint_related_notifications')->where('status', 'enabled')->isNotEmpty() ? 'checked' : '' }}>
                    <span class="slider round"></span>
                </label>
            </li>
        @endif
        @if (hasPermissionLike('event.'))
            <li>
                <p>Event Notification</p>
                <label class="switch">
                    <input type="checkbox" data-setting-name="new_event_notifications"
                        {{ $notificationSettings->where('name', 'new_event_notifications')->where('status', 'enabled')->isNotEmpty() ? 'checked' : '' }}>
                    <span class="slider round"></span>
                </label>
            </li>
        @endif
        @if (hasPermissionLike('notice.'))
            <li>
                <p>Notice Board Notification</p>
                <label class="switch">
                    <input type="checkbox" data-setting-name="new_notice_notifications"
                        {{ $notificationSettings->where('name', 'new_notice_notifications')->where('status', 'enabled')->isNotEmpty() ? 'checked' : '' }}>
                    <span class="slider round"></span>
                </label>
            </li>
        @endif
        @if (hasPermissionLike('sos.'))
            <li>
                <p>SOS Notification</p>
                <label class="switch">
                    <input type="checkbox" data-setting-name="sos_notifications"
                        {{ $notificationSettings->where('name', 'sos_notifications')->where('status', 'enabled')->isNotEmpty() ? 'checked' : '' }}>
                    <span class="slider round"></span>
                </label>
            </li>
        @endif

        @if (hasPermissionLike('property_listing.'))
            <li>
                <p>Property Listing Notification</p>
                <label class="switch">
                    <input type="checkbox" data-setting-name="property_related_notifications"
                        {{ $notificationSettings->where('name', 'property_related_notifications')->where('status', 'enabled')->isNotEmpty() ? 'checked' : '' }}>
                    <span class="slider round"></span>
                </label>
            </li>
        @endif
        @if (hasPermissionLike('poll.'))
            <li>
                <p>Poll Notification</p>
                <label class="switch">
                    <input type="checkbox" data-setting-name="poll_notifications"
                        {{ $notificationSettings->where('name', 'poll_notifications')->where('status', 'enabled')->isNotEmpty() ? 'checked' : '' }}>
                    <span class="slider round"></span>
                </label>
            </li>
        @endif
        @if (hasPermissionLike('billing.'))
            <li>
                <p>Billing Notification</p>
                <label class="switch">
                    <input type="checkbox" data-setting-name="bill_notifications"
                        {{ $notificationSettings->where('name', 'bill_notifications')->where('status', 'enabled')->isNotEmpty() ? 'checked' : '' }}>
                    <span class="slider round"></span>
                </label>
            </li>
        @endif
        @if (hasPermissionLike('refer_property.'))
            <li>
                <p>Refer Property Notification</p>
                <label class="switch">
                    <input type="checkbox" data-setting-name="refer_property_notifications"
                        {{ $notificationSettings->where('name', 'refer_property_notifications')->where('status', 'enabled')->isNotEmpty() ? 'checked' : '' }}>
                    <span class="slider round"></span>
                </label>
            </li>
        @endif
        @if (hasPermissionLike('document.'))
            <li>
                <p>Documents Related Notification</p>
                <label class="switch">
                    <input type="checkbox" data-setting-name="document_notifications"
                        {{ $notificationSettings->where('name', 'document_notifications')->where('status', 'enabled')->isNotEmpty() ? 'checked' : '' }}>
                    <span class="slider round"></span>
                </label>
            </li>
        @endif
        @if (hasPermissionLike('parcel.'))
            <li>
                <p>Parcel Related Notification</p>
                <label class="switch">
                    <input type="checkbox" data-setting-name="parcel_complaint_notifications"
                        {{ $notificationSettings->where('name', 'parcel_complaint_notifications')->where('status', 'enabled')->isNotEmpty() ? 'checked' : '' }}>
                    <span class="slider round"></span>
                </label>
            </li>
        @endif
    </ol>
</div>

@push('notification_settings_section')
    <script>
        $(document).ready(function() {
            // Handle toggle change
            $('.switch input[type="checkbox"]').on('change', function() {
                var csrfToken = $('meta[name="csrf-token"]').attr('content');
                const settingName = $(this).data('setting-name'); // Get the setting name
                const status = $(this).is(':checked') ? 'enabled' : 'disabled'; // Determine new status

                // Send an AJAX request
                $.ajax({
                    url: '{{ route($thisModule . '.notification.settings.update') }}',
                    type: 'POST',
                    data: {
                        _token: csrfToken, // CSRF token for security
                        name: settingName,
                        status: status
                    },
                    success: function(response) {
                        // alert(response.message); // Notify the user
                        if (response.hasOwnProperty('status')) {
                            toastr[response.status](response.message);
                        }
                    },
                    error: function(xhr) {
                        toastr.error('Error updating notification setting. Please try again.');
                    }
                });
            });
        });
    </script>
@endpush
