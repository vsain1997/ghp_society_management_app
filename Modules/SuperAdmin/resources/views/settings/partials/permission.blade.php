<div class="head_content">
    <div class="left_head">
        <h2>Access & Permissions</h2>
        <p>Manage admin access permission</p>
    </div>
</div>
<div class="filter_table_head">
    <div class="select_admin">
        <select name="select_admin" id="permission_of_user" class="form-select">
            <option value="">--Select Admin--</option>
            @if (count($roleOfAdmins) < 1)
                <option value="" disabled>No admins available</option>
            @endif
            @foreach ($roleOfAdmins as $admin)
                <option value="{{ $admin->user_id }}" selected>{{ $admin->name }}
                </option>
            @endforeach
        </select>
    </div>
</div>
<div class="bg-card">
    <div class="access_checkbox_wrapper">
        <div class="check_groups">
            <h5 id="selectLabel">Select All</h5>
            <div class="check_fields">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox"
                        id="selectAll">
                    {{-- <label class="form-check-label" for="parcelField1">
                        View Parcels Data
                    </label> --}}
                </div>
            </div>
        </div>
        <div class="check_groups">
            <h5>Society Properties</h5>
            <div class="check_fields">
                <div class="form-check">
                    <input class="form-check-input permission-checkbox" type="checkbox" value=""
                        data-permission="society.resident_units_view" id="societyField1">
                    <label class="form-check-label" for="societyField1">
                        View Society Properties
                    </label>
                </div>
            </div>
        </div>
        <div class="check_groups">
            <h5>Member Management</h5>
            <div class="check_fields">
                <div class="form-check">
                    <input class="form-check-input permission-checkbox" type="checkbox" data-permission="member.create"
                        value="" id="memberField1">
                    <label class="form-check-label" for="memberField1">
                        Add New Member
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input permission-checkbox" type="checkbox" data-permission="member.edit"
                        value="" id="memberField2">
                    <label class="form-check-label" for="memberField2">
                        Edit Memeber
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input permission-checkbox" type="checkbox" data-permission="member.delete"
                        value="" id="memberField3">
                    <label class="form-check-label" for="memberField3">
                        Delete Member
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input permission-checkbox" type="checkbox" data-permission="member.view"
                        value="" id="memberField4">
                    <label class="form-check-label" for="memberField4">
                        View Member
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input permission-checkbox" type="checkbox"
                        data-permission="member.status_change" value="" id="memberField4">
                    <label class="form-check-label" for="memberField4">
                        Change Status
                    </label>
                </div>
            </div>
        </div>
        <div class="check_groups">
            <h5>Staff Management</h5>
            <div class="check_fields">
                <div class="form-check">
                    <input class="form-check-input permission-checkbox" type="checkbox" data-permission="staff.create"
                        value="" id="staffField1">
                    <label class="form-check-label" for="staffField1">
                        Add New Staff
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input permission-checkbox" type="checkbox" data-permission="staff.edit"
                        value="" id="staffField2">
                    <label class="form-check-label" for="staffField2">
                        Edit Staff
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input permission-checkbox" type="checkbox" data-permission="staff.delete"
                        value="" id="staffField3">
                    <label class="form-check-label" for="staffField3">
                        Delete Staff
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input permission-checkbox" type="checkbox" data-permission="staff.view"
                        value="" id="staffField4">
                    <label class="form-check-label" for="staffField4">
                        View Staff
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input permission-checkbox" type="checkbox"
                        data-permission="staff.change_status" value="" id="staffField4">
                    <label class="form-check-label" for="staffField4">
                        Change Status
                    </label>
                </div>
            </div>
        </div>
        <div class="check_groups">
            <h5>Service Provider Category</h5>
            <div class="check_fields">
                <div class="form-check">
                    <input class="form-check-input permission-checkbox" type="checkbox"
                        data-permission="service_provider_category.create" value="" id="serviceProviderFeild1">
                    <label class="form-check-label" for="serviceProviderFeild1">
                        Add New Category
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input permission-checkbox" type="checkbox"
                        data-permission="service_provider_category.edit" value="" id="serviceProviderFeild2">
                    <label class="form-check-label" for="serviceProviderFeild2">
                        Edit Service Provider
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input permission-checkbox" type="checkbox"
                        data-permission="service_provider_category.delete" value="" id="serviceProviderFeild3">
                    <label class="form-check-label" for="serviceProviderFeild3">
                        Delete Service Provider
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input permission-checkbox" type="checkbox"
                        data-permission="service_provider_category.view" value="" id="serviceProviderFeild4">
                    <label class="form-check-label" for="serviceProviderFeild4">
                        View Service Provider
                    </label>
                </div>
            </div>
        </div>
        <div class="check_groups">
            <h5>Service Providers</h5>
            <div class="check_fields">
                <div class="form-check">
                    <input class="form-check-input permission-checkbox" type="checkbox"
                        data-permission="service_provider.create" value="" id="serviceProviderFeild1">
                    <label class="form-check-label" for="serviceProviderFeild1">
                        Add New Service Provider
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input permission-checkbox" type="checkbox"
                        data-permission="service_provider.edit" value="" id="serviceProviderFeild2">
                    <label class="form-check-label" for="serviceProviderFeild2">
                        Edit Service Provider
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input permission-checkbox" type="checkbox"
                        data-permission="service_provider.delete" value="" id="serviceProviderFeild3">
                    <label class="form-check-label" for="serviceProviderFeild3">
                        Delete Service Provider
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input permission-checkbox" type="checkbox"
                        data-permission="service_provider.view" value="" id="serviceProviderFeild4">
                    <label class="form-check-label" for="serviceProviderFeild4">
                        View Service Provider
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input permission-checkbox" type="checkbox"
                        data-permission="service_provider.change_status" value="" id="serviceProviderFeild4">
                    <label class="form-check-label" for="serviceProviderFeild4">
                        Change Status
                    </label>
                </div>
            </div>
        </div>
        <div class="check_groups">
            <h5>Notice Board</h5>
            <div class="check_fields">
                <div class="form-check">
                    <input class="form-check-input permission-checkbox" type="checkbox"
                        data-permission="notice.create" value="" id="noticeField1">
                    <label class="form-check-label" for="noticeField1">
                        Create Notices
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input permission-checkbox" type="checkbox" data-permission="notice.edit"
                        value="" id="noticeField2">
                    <label class="form-check-label" for="noticeField2">
                        Edit Notices
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input permission-checkbox" type="checkbox"
                        data-permission="notice.delete" value="" id="noticeField3">
                    <label class="form-check-label" for="noticeField3">
                        Delete Notices
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input permission-checkbox" type="checkbox" data-permission="notice.view"
                        value="" id="noticeField4">
                    <label class="form-check-label" for="noticeField4">
                        View Notices
                    </label>
                </div>
            </div>
        </div>
        <div class="check_groups">
            <h5>Events</h5>
            <div class="check_fields">
                <div class="form-check">
                    <input class="form-check-input permission-checkbox" data-permission="event.create"
                        type="checkbox" value="" id="eventsField1">
                    <label class="form-check-label" for="eventsField1">
                        Create Events
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input permission-checkbox" data-permission="event.edit" type="checkbox"
                        value="" id="eventsField2">
                    <label class="form-check-label" for="eventsField2">
                        Edit Events
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input permission-checkbox" data-permission="event.delete"
                        type="checkbox" value="" id="eventsField3">
                    <label class="form-check-label" for="eventsField3">
                        Delete Events
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input permission-checkbox" data-permission="event.view" type="checkbox"
                        value="" id="eventsField4">
                    <label class="form-check-label" for="eventsField4">
                        View Events
                    </label>
                </div>
                {{-- <div class="form-check">
                    <input class="form-check-input permission-checkbox" type="checkbox" value=""
                        id="eventsField4">
                    <label class="form-check-label" for="eventsField4">
                        Approve/Decline event requests
                    </label>
                </div> --}}
            </div>
        </div>
        <div class="check_groups">
            <h5>Complaints Category</h5>
            <div class="check_fields">
                <div class="form-check">
                    <input class="form-check-input permission-checkbox" type="checkbox"
                        data-permission="complaints_category.create" value="" id="complaintsCategoryField1">
                    <label class="form-check-label" for="complaintsCategoryField1">
                        Add Complaints Category
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input permission-checkbox" type="checkbox"
                        data-permission="complaints_category.edit" value="" id="complaintsCategoryField2">
                    <label class="form-check-label" for="complaintsCategoryField2">
                        Edit Complaints Category
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input permission-checkbox" type="checkbox"
                        data-permission="complaints_category.delete" value="" id="complaintsCategoryField3">
                    <label class="form-check-label" for="complaintsCategoryField3">
                        Delete Complaints Category
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input permission-checkbox" type="checkbox"
                        data-permission="complaints_category.view" value="" id="complaintsCategoryField4">
                    <label class="form-check-label" for="complaintsCategoryField4">
                        View Complaints Category
                    </label>
                </div>
            </div>
        </div>
        <div class="check_groups">
            <h5>Complaints</h5>
            <div class="check_fields">
                <div class="form-check">
                    <input class="form-check-input permission-checkbox" type="checkbox"
                        data-permission="complaints.assign" value="" id="complaintsField1">
                    <label class="form-check-label" for="complaintsField1">
                        Assign Service Provider
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input permission-checkbox" type="checkbox"
                        data-permission="complaints.view" value="" id="complaintsField2">
                    <label class="form-check-label" for="complaintsField2">
                        View Complaints
                    </label>
                </div>
            </div>
        </div>
        <div class="check_groups">
            <h5>Polls</h5>
            <div class="check_fields">
                <div class="form-check">
                    <input class="form-check-input permission-checkbox" data-permission="poll.create" type="checkbox"
                        value="" id="pollsField1">
                    <label class="form-check-label" for="pollsField1">
                        Create New Polls
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input permission-checkbox" data-permission="poll.edit" type="checkbox"
                        value="" id="pollsField2">
                    <label class="form-check-label" for="pollsField2">
                        Edit Polls
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input permission-checkbox" data-permission="poll.delete" type="checkbox"
                        value="" id="pollsField3">
                    <label class="form-check-label" for="pollsField3">
                        Delete Polls
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input permission-checkbox" data-permission="poll.view" type="checkbox"
                        value="" id="pollsField4">
                    <label class="form-check-label" for="pollsField4">
                        View Poll Results
                    </label>
                </div>
            </div>
        </div>
        <div class="check_groups">
            <h5>SOS Category</h5>
            <div class="check_fields">
                <div class="form-check">
                    <input class="form-check-input permission-checkbox" type="checkbox"
                        data-permission="sos_category.create" value="" id="sosCategoryField1">
                    <label class="form-check-label" for="sosCategoryField1">
                        Add SOS Category
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input permission-checkbox" type="checkbox"
                        data-permission="sos_category.edit" value="" id="sosCategoryField2">
                    <label class="form-check-label" for="sosCategoryField2">
                        Edit SOS Category
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input permission-checkbox" type="checkbox"
                        data-permission="sos_category.delete" value="" id="sosCategoryField3">
                    <label class="form-check-label" for="sosCategoryField3">
                        Delete SOS Category
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input permission-checkbox" type="checkbox"
                        data-permission="sos_category.view" value="" id="sosCategoryField4">
                    <label class="form-check-label" for="sosCategoryField4">
                        View SOS Category
                    </label>
                </div>
            </div>
        </div>
        <div class="check_groups">
            <h5>SOS</h5>
            <div class="check_fields">
                <div class="form-check">
                    <input class="form-check-input permission-checkbox" type="checkbox" data-permission="sos.view"
                        value="" id="sosField1">
                    <label class="form-check-label" for="sosField1">
                        View SOS Data
                    </label>
                </div>
                {{-- <div class="form-check">
                    <input class="form-check-input permission-checkbox" data-permission="sos.status_change"
                        type="checkbox" value="" id="sosField2">
                    <label class="form-check-label" for="sosField2">
                        Change Status
                    </label>
                </div> --}}
            </div>
        </div>
        <div class="check_groups">
            <h5>Visitors</h5>
            <div class="check_fields">
                <div class="form-check">
                    <input class="form-check-input permission-checkbox" type="checkbox"
                        data-permission="visitor.view" value="" id="visitorField1">
                    <label class="form-check-label" for="visitorField1">
                        View Visitors Data
                    </label>
                </div>
            </div>
        </div>
        <div class="check_groups">
            <h5>Property Listings</h5>
            <div class="check_fields">
                <div class="form-check">
                    <input class="form-check-input permission-checkbox" type="checkbox"
                        data-permission="property_listing.view" value="" id="propertylistingField1">
                    <label class="form-check-label" for="propertylistingField1">
                        View Rent/Sell Listings
                    </label>
                </div>
            </div>
        </div>
        <div class="check_groups">
            <h5>Billing</h5>
            <div class="check_fields">
                <div class="form-check">
                    <input class="form-check-input permission-checkbox" type="checkbox"
                        data-permission="billing.create" value="" id="billingField1">
                    <label class="form-check-label" for="billingField1">
                        Create Billing
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input permission-checkbox" type="checkbox"
                        data-permission="billing.edit" value="" id="billingField2">
                    <label class="form-check-label" for="billingField2">
                        Edit Billing
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input permission-checkbox" type="checkbox"
                        data-permission="billing.delete" value="" id="billingField3">
                    <label class="form-check-label" for="billingField3">
                        Delete Billing
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input permission-checkbox" type="checkbox"
                        data-permission="billing.view" value="" id="billingField4">
                    <label class="form-check-label" for="billingField4">
                        View Billing
                    </label>
                </div>
            </div>
        </div>
        <div class="check_groups">
            <h5>Refer Property</h5>
            <div class="check_fields">
                <div class="form-check">
                    <input class="form-check-input permission-checkbox" type="checkbox"
                        data-permission="refer_property.view" value="" id="referPropertyField1">
                    <label class="form-check-label" for="referPropertyField1">
                        View Refer Property
                    </label>
                </div>
            </div>
        </div>
        <div class="check_groups">
            <h5>Documents</h5>
            <div class="check_fields">
                <div class="form-check">
                    <input class="form-check-input permission-checkbox" type="checkbox"
                        data-permission="document.request" value="" id="documentField1">
                    <label class="form-check-label" for="documentField1">
                        Request & Download Document
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input permission-checkbox" type="checkbox"
                        data-permission="document.upload_send" value="" id="documentField2">
                    <label class="form-check-label" for="documentField2">
                        Upload & Send
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input permission-checkbox" type="checkbox"
                        data-permission="document.view" value="" id="documentField3">
                    <label class="form-check-label" for="documentField3">
                        View Requests
                    </label>
                </div>
            </div>
        </div>
        <div class="check_groups">
            <h5>Parcels</h5>
            <div class="check_fields">
                <div class="form-check">
                    <input class="form-check-input permission-checkbox" type="checkbox" data-permission="parcel.view"
                        value="" id="parcelField1">
                    <label class="form-check-label" for="parcelField1">
                        View Parcels Data
                    </label>
                </div>
            </div>
        </div>

        <div>
            <button type="button" id="savePermissions" class="bg_theme_btn">
                Save
            </button>
        </div>
    </div>
</div>
@push('permission_section')
    <script>

        function updateSelectAllCheckbox() {
            const totalCheckboxes = $('.permission-checkbox').length;
            const checkedCheckboxes = $('.permission-checkbox:checked').length;

            // Update "Select All" checkbox based on state
            $('#selectAll').prop('checked', totalCheckboxes > 0 && totalCheckboxes === checkedCheckboxes);
        }

        $('#permission_of_user').on('change', function() {
            const userId = $(this).val();
            if (userId) {
                $.ajax({
                    url: '{{ route($thisModule . '.user.permission.get') }}', // Adjust this URL to match your route
                    type: 'GET',
                    data: {
                        user_id: userId
                    },
                    success: function(response) {
                        // Clear all checkboxes first
                        $('.form-check-input').prop('checked', false);

                        if (response.permissions && response.permissions.length > 0) {
                            response.permissions.forEach(function(permission) {
                                // Match checkboxes based on data-permission attribute
                                $(`.form-check-input[data-permission="${permission}"]`).prop(
                                    'checked', true);
                            });
                        }
                        updateSelectAllCheckbox();
                    },
                    error: function() {
                        toastr.error('Failed to load permissions. Please try again');
                    }
                });
            } else {
                // Clear all checkboxes if no user is selected
                $('.form-check-input').prop('checked', false);
            }
        });

        // $(document).on('change', '.permission-checkbox', function() {
        //     let userId = $('#permission_of_user').val();
        //     let permissionName = $(this).data('permission');
        //     let isChecked = Boolean($(this).is(':checked'));

        //     if (!userId) {
        //         toastr.error('Please select an admin first');
        //         $(this).prop('checked', !isChecked); // Revert change
        //         return;
        //     }
        //     // update-permission
        //     $.ajax({
        //         url: '{{ route($thisModule . '.permission.update') }}', // Your Laravel route
        //         type: 'POST',
        //         data: {
        //             user_id: userId,
        //             permission: permissionName,
        //             is_checked: isChecked,
        //             _token: '{{ csrf_token() }}' // Laravel CSRF protection
        //         },
        //         success: function(response) {

        //             if (response.hasOwnProperty('status')) {
        //                 toastr[response.status](response.message);
        //             }
        //         },
        //         error: function(xhr) {
        //             $(this).prop('checked', !isChecked); // Revert change on error
        //             toastr.error('Error occurred ! Please try again');
        //         }
        //     });
        // });

    $(document).ready(function () {
        $('#permission_of_user').trigger('change');
        // Handle "Select All" functionality
        $(document).on('change', '#selectAll', function () {
            let userId = $('#permission_of_user').val();
            if (!userId) {
                toastr.error('Please select an admin first');
                $(this).prop('checked', !isChecked); // Revert change
                return;
            }
            const isChecked = $(this).is(':checked');
            $('.permission-checkbox').prop('checked', isChecked); // Select/deselect all
        });

        // Handle Save Permissions functionality
        $(document).on('click', '#savePermissions', function () {
            const userId = $('#permission_of_user').val();
            if (!userId) {
                toastr.error('Please select an admin first');
                return;
            }

            // Collect all permissions and their states
            const permissions = [];
            $('.permission-checkbox').each(function () {
                permissions.push({
                    permission: $(this).data('permission'),
                    is_checked: $(this).is(':checked') ? 1 : 0,
                });
            });

            // Send bulk update request
            $.ajax({
                url: '{{ route($thisModule . ".permission.update.bulk") }}', // Update to your bulk route
                type: 'POST',
                data: {
                    user_id: userId,
                    permissions: permissions,
                    _token: '{{ csrf_token() }}' // Laravel CSRF protection
                },
                success: function (response) {
                    toastr[response.status](response.message);
                },
                error: function () {
                    toastr.error('An error occurred while saving permissions');
                },
                complete: function () {
                    updateSelectAllCheckbox();
                }
            });
        });
    });

    </script>
@endpush
