@extends($thisModule . '::layouts.master')

@section('title', 'Staff')

@section('content')
<div class="right_main_body_content members_page">
    <div class="head_content">
        <div class="left_head">
            <h2>Staff</h2>
        </div>
        <!-- Button trigger modal -->

        <button type="button" class="bg_theme_btn" id="addStaffModalOpen" data-bs-toggle="modal"
            data-bs-target="#addStaffModal">
            <svg width="14" height="15" viewBox="0 0 14 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M13.0005 8.5H8.00049V13.5C8.00049 14.05 7.55049 14.5 7.00049 14.5C6.45049 14.5 6.00049 14.05 6.00049 13.5V8.5H1.00049C0.450488 8.5 0.000488281 8.05 0.000488281 7.5C0.000488281 6.95 0.450488 6.5 1.00049 6.5H6.00049V1.5C6.00049 0.95 6.45049 0.5 7.00049 0.5C7.55049 0.5 8.00049 0.95 8.00049 1.5V6.5H13.0005C13.5505 6.5 14.0005 6.95 14.0005 7.5C14.0005 8.05 13.5505 8.5 13.0005 8.5Z"
                    fill="white" />
            </svg>
            Add Staff
        </button>

    </div>
    <div class="custom_table_wrapper">
        <div class="filter_table_head">
            <div class="search_wrapper search-members-gstr">
                <form action="{{ route($thisModule . '.staff.index') }}" method="GET">
                    {{-- @csrf --}}
                    <div class="input-group">
                        <input type="hidden" name="sid" value="{{ session('__selected_society__') }}">
                        <div class="filter-secl">
                            <select name="status" class="form-control">
                                <option value="none" disabled>--Choose Status--</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active
                                </option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>
                                    Inactive
                                </option>
                            </select>

                            <select name="search_for" class="form-control">
                                <option value="none" disabled>--Search By--</option>
                                <option value="name" {{ request('search_for') == 'name' ? 'selected' : '' }}>Name
                                </option>
                                <option value="phone" {{ request('search_for') == 'phone' ? 'selected' : '' }}>
                                    Contact
                                </option>
                                <option value="email" {{ request('search_for') == 'email' ? 'selected' : '' }}>
                                    Email
                                </option>
                            </select>
                        </div>

                        <div class="search-full-box">
                            <input type="search" name="search" id="search" placeholder="Search"
                                value="{{ request('search') }}">
                            <button type="submit" class="bg_theme_btn">
                                Filter
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="right_filters">
                {{-- <h2>Staff Listing </h2> --}}
                {{-- <button class="sortby">
                    <svg width="25" height="24" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M3.13306 7H21.1331" stroke="#020015" stroke-width="1.5" stroke-linecap="round" />
                        <path d="M6.13306 12H18.1331" stroke="#020015" stroke-width="1.5" stroke-linecap="round" />
                        <path d="M10.1331 17H14.1331" stroke="#020015" stroke-width="1.5" stroke-linecap="round" />
                    </svg>
                    Sort By
                </button>
                <button class="filterbtn">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M22 6.5H16" stroke="#020015" stroke-width="1.5" stroke-miterlimit="10"
                            stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M6 6.5H2" stroke="#020015" stroke-width="1.5" stroke-miterlimit="10"
                            stroke-linecap="round" stroke-linejoin="round" />
                        <path
                            d="M10 10C11.933 10 13.5 8.433 13.5 6.5C13.5 4.567 11.933 3 10 3C8.067 3 6.5 4.567 6.5 6.5C6.5 8.433 8.067 10 10 10Z"
                            stroke="#020015" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round"
                            stroke-linejoin="round" />
                        <path d="M22 17.5H18" stroke="#020015" stroke-width="1.5" stroke-miterlimit="10"
                            stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M8 17.5H2" stroke="#020015" stroke-width="1.5" stroke-miterlimit="10"
                            stroke-linecap="round" stroke-linejoin="round" />
                        <path
                            d="M14 21C15.933 21 17.5 19.433 17.5 17.5C17.5 15.567 15.933 14 14 14C12.067 14 10.5 15.567 10.5 17.5C10.5 19.433 12.067 21 14 21Z"
                            stroke="#020015" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                    Filter
                </button> --}}
            </div>
        </div>
        <div class="table-responsive">
            <table width="100%" cellpadding="0" cellspacing="0">
                <thead>
                    <tr>
                        <th class="text-center">Name</th>
                        <th class="text-center">Role</th>
                        <th class="text-center">Phone</th>
                        <th class="text-center">Email</th>
                        <th class="text-center">Location</th>
                        <th class="text-center">status</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $sl = 0;
                    @endphp
                    @if ($staffs && !$staffs->isEmpty())
                    @foreach ($staffs as $staff)
                    @php
                    $sl++;
                    $role = '';
                    if ($staff->role == 'staff') {
                    $role = 'Maintenance Staff';
                    } elseif ($staff->role == 'staff_security_guard') {
                    $role = 'Security Guard';
                    }else{
                        $role = ucwords(str_replace('_',' ',str_replace('staff', '', $staff->role)));
                    }
                    @endphp
                    <tr>
                        <td class="text-center">{{ $staff->name }}</td>
                        <td class="text-center">{{ $role }}</td>
                        <td class="text-center">{{ $staff->phone }}</td>
                        <td class="text-center">{{ $staff->email }}</td>

                        <td class="text-center">
                            @if (strlen($staff->address) > 50)
                            {{ substr($staff->address, 0, 47) . '...' }}
                            @else
                            {{ $staff->address }}
                            @endif
                        </td>
                        <td class="text-center">
                            <input type="hidden" name="statusVal" value="{{ parseStatus($staff->status, 0) }}">
                            <div class="status_select">
                                <select name="status" data-id="{{ $staff->id }}" class="statusOption form-select">
                                    <option value="active" {{ parseStatus($staff->status, 1) }}>Active
                                    </option>
                                    <option value="inactive" {{ parseStatus($staff->status, 2) }}>Inactive</option>
                                </select>

                            </div>
                        </td>
                        <td class="text-center">
                            <div class="actions">
                                @if ($staff->daily_help)
                                    <a class="" title="Assign To Member" href="javascript:void(0)" data-user-id="{{ $staff->user_id }}" data-bs-toggle="modal"
                                        data-bs-target="#assignMember">
                                        <i class="fa-solid fa-user-plus" style="margin-right:8px;margin-top:14px;color:#282638"></i>
                                    </a>
                                @endif
                                <a class="edit edit-icon" href="javascript:void(0)" id="{{ $staff->id }}">
                                    <img src="{{ url($thisModule) }}/img/edit.png" alt="edit">
                                </a>
                                <a class="view" href="{{ route($thisModule . '.staff.details', ['id' => $staff->id]) }}"
                                    id="{{ $staff->id }}">
                                    <img src="{{ url($thisModule) }}/img/eye.png" alt="eye">
                                </a>
                                <a class="delete delete-icon" href="javascript:void(0)" data-id="{{ $staff->id }}">
                                    <img src="{{ url($thisModule) }}/img/delete.png" alt="delete">
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                    @else
                    <tr>
                        <td colspan="7" class="text-center"> No Data Found </td>
                    </tr>
                    @endif
                </tbody>
            </table>
            <div class="table_bottom_box">
                {{-- Pagination Links --}}
                <div class="d-flex justify-content-between p-2 mt-2 mb-2">
                    <div>
                        Showing {{ $staffs->firstItem() }} to {{ $staffs->lastItem() }} of
                        {{ $staffs->total() }} results
                    </div>
                    <div>
                        {{ $staffs->links('vendor.pagination.bootstrap-5') }} {{-- Bootstrap 5 pagination view --}}
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade custom_Modal" id="addStaffModal" tabindex="-1" aria-labelledby="addStaffModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content ">
            <div class="modal-header">
                <h3 class="text-white" id="modalHeadTxt">Add Staff</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="custom_form">
                    <form method="POST" action="{{ route($thisModule . '.staff.store') }}" id="addStaffForm" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col d-none">
                                <div class="form-group">
                                    <input type="hidden" name="id" id="id">
                                    <input type="hidden" name="user_id" id="user_id">
                                    <input type="hidden" name="created_by" value="{{ auth()->user()->id }}">
                                    <label for="society_id">Society</label>
                                    <select name="society_id" id="society_id" class="form-select form-control">
                                        <option value="">--select--</option>
                                        @if (!empty($mySocietys))
                                        @foreach ($mySocietys as $society)
                                        <option value="{{ $society->id }}" selected>
                                            {{ $society->name }}</option>
                                        @endforeach
                                        @else
                                        @php $cnt1 = 0; @endphp
                                        @foreach ($__societies__ as $society)
                                        @if (!session('__selected_society__'))
                                        @if ($cnt1 == 0)
                                        <option value="{{ $society->id }}" selected>
                                            {{ $society->name }}</option>
                                        @php
                                        session(['__selected_society__' => $society->id]);
                                        @endphp
                                        @endif
                                        @endif

                                        @if (session('__selected_society__') == $society->id)
                                        <option value="{{ $society->id }}" selected>
                                            {{ $society->name }}</option>
                                        @else
                                        <option value="{{ $society->id }}">{{ $society->name }}
                                        </option>
                                        @endif
                                        @php $cnt1++; @endphp
                                        @endforeach
                                        @endif
                                    </select>
                                    <span class="text-danger err"></span>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label for="name">Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name" class="form-control">
                                    <span class="text-danger err"></span>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-grooup">
                                    <label for="role">Role <span class="text-danger">*</span></label>
                                    <select name="role" id="role" class="form-select form-control ">
                                        <option value="" selected>--select--</option>
                                        @foreach ($staffRoles as $role)
                                            <option value="{{ $role->name }}">{{ $role->label }}</option>
                                        @endforeach
                                        <option value="other">Other</option>
                                    </select>
                                    <span class="text-danger err"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row" id="other_role_name_block">
                            <div class="form-grooup">
                                <label for="other_role_name">Other Role Name</label>
                                <input type="text" name="other_role_name" id="other_role_name" class="form-control">
                                <span class="text-danger err"></span>
                            </div>
                        </div>
                        <div class="row" id="service_category_block">
                            <div class="form-grooup">
                                <label for="complaint_category_id">Complaint Category</label>
                                <select name="complaint_category_id" id="complaint_category_id"
                                    class="form-select form-control ">
                                    <option value="" selected>--select--</option>
                                    @foreach ($complaint_categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                <span class="text-danger err"></span>
                            </div>
                        </div>
                        <div class="row" id="daily_help_block">
                            <div class="form-check d-inline-flex">
                                <input type="checkbox" name="daily_help" id="daily_help" class="form-check-input" style="width: 6%">
                                <label style="padding-top: 2%;padding-left: 1%;">Staff As Daily Help</label>
                                <span class="text-danger err"></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="form-grooup">
                                    <label for="employee_id">Employee Id <span class="text-danger">*</span></label>
                                    <input type="text" name="employee_id" id="employee_id" class="form-control">
                                    <span class="text-danger err"></span>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-grooup">
                                    <label for="assigned_area">Assigned Area</label>
                                    <input type="text" name="assigned_area" id="assigned_area" class="form-control">
                                    <span class="text-danger err"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="form-grooup">
                                    <label for="gender">Gender <span class="text-danger">*</span></label>
                                    <select name="gender" id="gender" class="form-select form-control ">
                                        <option value="" selected>--select--</option>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                    </select>
                                    <span class="text-danger err"></span>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-grooup">
                                    <label for="dob">DOB</label>
                                    <div class="custom-plac">
                                        <input type="date" name="dob" id="dob" class="form-control remove-date">
                                        <span class="none-plac date-plac err">Date</span>
                                    </div>
                                    <span class="text-danger err"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="form-group">
                                    <label for="phone">Phone <span class="text-danger">*</span></label>
                                    <input type="text" name="phone" id="phone" class="form-control phone-input-restrict">
                                    <span class="text-danger err"></span>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label for="email">Email <span class="text-danger">*</span></label>
                                    <input type="email" name="email" id="email" class="form-control">
                                    <span class="text-danger err"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-grooup">
                                <label for="role">Address <span class="text-danger">*</span></label>
                                <input type="text" name="location" id="location" class="form-control">
                                <span class="text-danger err"></span>
                            </div>
                        </div>
                        <hr>
                        <p class="pb-1">Shift Details </p>
                        <div class="row">
                            <div class="col">
                                <div class="date-fild">
                                    <label for="shift_from">Shift From</label>
                                    <input type="text" name="shift_from" id="shift_from" class="form-control">
                                    <span class="text-danger err" id="timeFromError"></span>
                                </div>
                            </div>
                            <div class="col">
                                <div class="date-fild">
                                    <label for="shift_to">Shift To</label>
                                    <input type="text" name="shift_to" id="shift_to" class="form-control">
                                    <span class="text-danger err" id="timeToError"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="form-grooup">
                                    <label for="off_days">Off Days</label>
                                    <select name="off_days[]" id="off_days" class="form-select form-control days" multiple>
                                        <option value="" selected>None</option>
                                        <option value="Sunday">Sunday</option>
                                        <option value="Monday">Monday</option>
                                        <option value="Tuesday">Tuesday</option>
                                        <option value="Wednesday">Wednesday</option>
                                        <option value="Thursday">Thursday</option>
                                        <option value="Friday">Friday</option>
                                        <option value="Saturday">Saturday</option>
                                    </select>
                                    <span class="text-danger err"></span>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <p class="pb-1">Emergency Contact </p>
                        <div class="row">
                            <div class="col">
                                <div class="form-group">
                                    <label for="emer_name">Name</label>
                                    <input type="text" name="emer_name" id="emer_name" class="form-control">
                                    <span class="text-danger err"></span>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label for="emer_relation">Relation</label>
                                    <input type="text" name="emer_relation" id="emer_relation" class="form-control">
                                    <span class="text-danger err"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="form-group">
                                    <label for="emer_phone">Phone</label>
                                    <input type="text" name="emer_phone" id="emer_phone" class="form-control phone-input-restrict">
                                    <span class="text-danger err"></span>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <p class="pb-1">Employment Details </p>
                        <div class="row">
                            <div class="col">
                                <div class="form-group">
                                    <label for="date_of_join">Date Of Join</label>
                                    <input type="date" name="date_of_join" id="date_of_join" class="form-control">
                                    <span class="text-danger err"></span>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label for="contract_end_date">Contract End Date</label>
                                    <input type="date" name="contract_end_date" id="contract_end_date" class="form-control">
                                    <span class="text-danger err"></span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col">
                                <div class="form-group">
                                    <label for="monthly_salary">Monthly Salary</label>
                                    <input type="text" name="monthly_salary" id="monthly_salary" class="form-control">
                                    <span class="text-danger err"></span>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-grooup">
                                    <label for="card_type">Card Type <span class="text-danger">*</span></label>
                                    <select name="card_type" id="card_type" class="form-select form-control" data-modal="addStaffModal" onchange="cardNumbersValidation(this)">
                                        <option value="" selected>--select--</option>
                                        <option value="Aadhaar Card">Aadhaar Card</option>
                                        <option value="Voter ID Card">Voter ID Card</option>
                                    </select>
                                    <span class="text-danger err"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="form-grooup">
                                    <label for="card_number">Card No <span class="text-danger">*</span></label>
                                    <input type="text" name="card_number" id="card_number" class="form-control">
                                    <span class="text-danger err"></span>
                                </div>
                            </div>

                            <div class="col">
                                <div class="form-grooup">
                                    <label for="card_file">Upload Card <span class="text-danger">*</span></label>
                                    <input type="file" name="card_file" id="card_file" class="form-control" accept=".jpg,.jpeg,.png" onchange="validateFile(this)">
                                    <span class="text-danger err"></span>
                                </div>
                            </div>
                        </div>

                        <div class="save-close-btn">
                            <button type="button" class="border_theme_btn close-btn cancel_btn"
                                data-bs-dismiss="modal">Close</button>
                            <button type="button" data-formtype="add" id="submitaddStaffForm"
                                class="bg_theme_btn">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- Model2::assign-resident --}}
<div class="modal fade custom_Modal" id="assignMember" tabindex="-1" aria-labelledby="assignMemberLabel" aria-hidden="true" data-staff-user-id="" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-white" id="assignMemberLabel">Assign To Member</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="container">
                    <form method="POST" action="{{ route($thisModule . '.staff.assignMember') }}" id="assignMemberForm">
                        @csrf
                        <div id="memberShiftContainer">
                            {{-- <div class="row mb-2 member-shift-row">
                                <div class="col-md-4">
                                    <input type="hidden" name="staff_user_id" id="staff_user_id">
                                    <label class="form-label">Choose Member</label>
                                    <select name="user_id[]" class="select-member form-select form-control">
                                        <option value="">--select--</option>
                                        @if (!empty($societyResidents))
                                        @foreach ($societyResidents as $resident)
                                        <option data-floor="{{ $resident->floor_number }}"
                                            data-block="{{ $resident->block_name }}"
                                            data-unitType="{{ $resident->unit_type }}"
                                            data-aprtNo="{{ $resident->aprt_no }}" data-phone="{{ $resident->phone }}"
                                            data-name="{{ $resident->name }}" value="{{ $resident->user_id }}">
                                            {{ $resident->name }}</option>
                                        @endforeach
                                        @endif
                                    </select>
                                    <span class="text-danger err"></span>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Shift From</label>
                                    <input type="time" name="m_shift_from[]" class="form-control">
                                    <span class="text-danger err"></span>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Shift To</label>
                                    <input type="time" name="m_shift_to[]" class="form-control">
                                    <span class="text-danger err"></span>
                                </div>
                                <div class="col-md-2 d-flex align-items-end" style="padding-bottom: 12px">
                                    <button type="button" class="btn btn-primary add-row">+</button>
                                </div>
                            </div> --}}
                            Loading...
                        </div>

                        <div class="save-close-btn pt-4">
                            <button type="button" class="border_theme_btn close-btn cancel_btn"
                            data-bs-dismiss="modal">Close</button>
                            <button type="button"
                            class="bg_theme_btn" id="assignMemberFormBtn">Assign</button>
                        </div>
                    </form>
                </div>
            </div>
            {{-- <div class="modal-footer"> --}}
                {{-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button> --}}
                {{-- <button type="button" class="btn btn-primary">Save Changes</button> --}}

            {{-- </div> --}}


        </div>
    </div>
</div>

@endsection

@push('footer-script')
<script>
    function cardNumbersValidation(e) {
        var type = $(e).val();
        const modal = $(e).data('modal');
        const cardInput = $('#' + modal).find('#card_number');

        cardInput.val('');
        // Set maxlength
        if (type == 'Aadhaar Card') {
            cardInput.attr('maxlength', '12');
        } else if (type == 'Voter ID Card') {
            cardInput.attr('maxlength', '10');
        }

        // Remove previous event handlers to avoid stacking
        cardInput.off('input');

        // Input restriction
        cardInput.on('input', function () {
            let value = $(this).val();
            if (type == 'Aadhaar Card') {
                // Keep only digits
                $(this).val(value.replace(/\D/g, ''));
            } else {
                // Keep only alphanumeric characters
                $(this).val(value.replace(/[^a-zA-Z0-9]/g, ''));
            }
        });
    }
</script>

<script>
    const timePicker_shift_from = flatpickr("#shift_from", {
            enableTime: true,
            noCalendar: true,
            dateFormat: "h:i K",
            time_24hr: false,
            defaultHour: 12,
            defaultMinute: 0,
            onOpen: function() {
                document.getElementById("timeFromError").textContent = "";
            },
            onChange: function(selectedDates, dateStr) {
                console.log("Selected Time:", dateStr);
            }
        });
    const timePicker_shift_to = flatpickr("#shift_to", {
            enableTime: true,
            noCalendar: true,
            dateFormat: "h:i K",
            time_24hr: false,
            defaultHour: 12,
            defaultMinute: 0,
            onOpen: function() {
                document.getElementById("timeToError").textContent = "";
            },
            onChange: function(selectedDates, dateStr) {
                console.log("Selected Time:", dateStr);
            }
        });
</script>
<script>
    $(".members_page").on('click', '#addStaffModalOpen', function (event) {
        // set modal form
        $('#addStaffForm').attr('action',
            '{{ route($thisModule . '.staff.store') }}'
        );
        $('#submitaddStaffForm').attr('data-formtype', 'add');
        $('#modalHeadTxt').text('Add Staff');
        $('#submitaddStaffForm').text('Submit');

        // reset form data
        $('#addStaffForm').find(
            'input:not([name="_token"],[name^="society_id"],[name^="created_by"]), select, textarea').each(
                function () {
                    $(this).val('');
                });
        $('#society_id').val({{ session('__selected_society__') }});
    $('.err').text('');
        });
</script>
{{-- add + edit form validation and submit --}}
<script>
    $(document).ready(function () {

        $('.days').select2({
            width: '100%',
            dropdownParent: $('#addStaffModal'),
            minimumResultsForSearch: 0 // Always show the search box
        });
        // disable modal outside click
        $('#addStaffModal').modal({
            backdrop: 'static',
            keyboard: false
        });

        $('#daily_help_block').hide();
        $('#service_category_block').hide();
        $('#other_role_name_block').hide();
        $('#addStaffForm').on('change', '#role', function () {
            if ($(this).val() == 'staff') {

                $('#daily_help_block').hide();
                $('#other_role_name_block').hide();
                $('#service_category_block').show();
            } else if ($(this).val() == 'other') {

                $('#service_category_block').hide();
                $('#other_role_name_block').show();
                $('#daily_help_block').show();
            } else if ($(this).val() == 'staff_security_guard') {

                $('#service_category_block').hide();
                $('#other_role_name_block').show();
                $('#daily_help_block').hide();
            }else {
                $('#service_category_block').hide();
                $('#other_role_name_block').hide();
                $('#daily_help_block').show();
            }
        });
        // check form1 validation and switch to next form2
        $(".modal").on('click', '#submitaddStaffForm', async function (event) {
            event.preventDefault();
            // loader add
            $('#loader').css('width', '50%');
            $('#loader').fadeIn();
            $('#blockOverlay').fadeIn();

            // let formType = $('#submitaddStaffForm').data('formtype');
            let formType = $('#id').val();
            if (formType > 0 && formType) {
                formType = 'edit';
            } else {
                formType = 'add';
            }
            // console.log('call', formType);

            let validationStatus = await validateForm(formType);
            if (validationStatus != 0) {
                //loader removed
                $('#loader').css('width', '100%');
                $('#loader').fadeOut();
                $('#blockOverlay').fadeOut();

                // toastr.error('Kindly complete all fields accurately !')
                return false;
            }

            //direct submit for add and ajax submit for edit
            if (formType == 'add') {
                //loader removed
                $('#loader').css('width', '100%');
                $('#loader').fadeOut();
                $('#blockOverlay').fadeOut();

                $('#addStaffForm').submit();
            } else {
                //on modal-cancel relaod page to show fresh updated data
                $('#addStaffForm').find('.cancel_btn').attr('onclick',
                    'window.location.reload()');
                let formData = $('#addStaffForm').serialize();
                $.ajax({
                    url: $('#addStaffForm').attr('action'),
                    method: 'POST',
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    success: function (response) {
                        //loader removed
                        $('#loader').css('width', '100%');
                        $('#loader').fadeOut();
                        $('#blockOverlay').fadeOut();

                        toastr[response.status](response.message);
                    },
                    error: function (xhr, status, error) {
                        //loader removed
                        $('#loader').css('width', '100%');
                        $('#loader').fadeOut();
                        $('#blockOverlay').fadeOut();

                        toastr[response.status](response.message);
                    }
                });

                setTimeout(function () {
                    location.reload();
                }, 2000);
            }
        });
    });

    //validate
    async function validateForm(formType) {
        let hasError = 0;
        let name = $('#name').val().trim();
        let role = $('#role').val().trim();
        let other_role_name = $('#other_role_name').val().trim();
        let complaint_category_id = $('#complaint_category_id').val().trim();
        let employee_id = $('#employee_id').val().trim();
        let assigned_area = $('#assigned_area').val().trim();
        let gender = $('#gender').val().trim();
        let dob = $('#dob').val().trim();
        let phone = $('#phone').val().trim();
        let email = $('#email').val().trim();
        let location = $('#location').val().trim();
        let userId = $('#user_id').val().trim();

        let emer_name = $('#emer_name').val().trim();
        let emer_relation = $('#emer_relation').val().trim();
        let emer_phone = $('#emer_phone').val().trim();

        let date_of_join = $('#date_of_join').val().trim();
        let contract_end_date = $('#contract_end_date').val().trim();
        let monthly_salary = $('#monthly_salary').val().trim();

        let card_type = $('#card_type').val().trim();
        let card_number = $('#card_number').val().trim();
        let card_file = $('#card_file').val().trim();
        $('.err').text('');

        let resultPhone;
        let resultEmail;
        if (formType == 'add') {
            resultPhone = await checkDuplicate('phone', phone);
            resultEmail = await checkDuplicate('email', email);
        } else {
            resultPhone = await checkDuplicate('phone', phone, userId);
            resultEmail = await checkDuplicate('email', email, userId);
        }

        // Validate name
        if (name === '') {
            $('#name').siblings('.err').text('Name is required');
            hasError = 1;
        }

        if (role === '') {
            $('#role').siblings('.err').text('Please Select Role');
            hasError = 1;
        }

        if (role == 'staff') {
            if (complaint_category_id === '') {
                $('#complaint_category_id').siblings('.err').text('Please Select Category');
                hasError = 1;
            }
        }

        if (role == 'other') {
            if (other_role_name === '') {
                $('#other_role_name').siblings('.err').text('Please specify other role name');
                hasError = 1;
            }
        }

        if (employee_id === '') {
            $('#employee_id').next('.err').text('Required');
            hasError = 1;
        }
        // if (assigned_area === '') {
        //     $('#assigned_area').next('.err').text('Required');
        //     hasError = 1;
        // }

        if (gender === '') {
            $('#gender').next('.err').text('Required');
            hasError = 1;
        }

        if (dob === '') {
            $('#dob').next('.err').text('Required');
            hasError = 1;
        }else{
                let currentYear = new Date().getFullYear();
                // Calculate the allowed max year (current year - 15)
                let restrictedYear = currentYear - 15;
                // Set the max date as December 31st of the restricted year
                let maxDate = restrictedYear + "-12-31";

                let selectedDate = new Date(dob);
                let maxAllowedDate = new Date(maxDate);

                // Check if the entered date is beyond the allowed max
                if (selectedDate > maxAllowedDate) {
                    $('#dob').next('.err').text('At least 15 years old.');
                    hasError = 1;
                }
            }

        // Validate phone
        if (phone === '') {
            $('#phone').next('.err').text('Required');
            hasError = 1;
        } else if (!validatePhone(phone)) {
            $('#phone').next('.err').text('Invalid');
            hasError = 1;
        } else if (resultPhone) {
            $('#phone').next('.err').text('Already exists !');
            hasError = 1;
        }

        // Validate email
        if (email === '') {
            $('#email').next('.err').text('Required');
            hasError = 1;
        } else if (!validateEmail(email)) {
            $('#email').next('.err').text('Invalid');
            hasError = 1;
        } else if (resultEmail) {
            $('#email').next('.err').text('Already exists !');
            hasError = 1;
        }

        if (location === '') {
            $('#location').siblings('.err').text('Required');
            hasError = 1;
        }

        if (isNotEmpty(emer_name) && !isNaN(emer_name)) {
            $('#emer_name').next('.err').text('Invalid');
            hasError = 1;
        }
        if (isNotEmpty(emer_relation) && !isNaN(emer_relation)) {
            $('#emer_relation').next('.err').text('Invalid');
            hasError = 1;
        }
        $phoneError = $('#emer_phone').next('.err');
        if (isNotEmpty(emer_phone) && isInvalidPhone(emer_phone, $phoneError, phoneErrMsg_invalid = "Invalid")) {
            hasError = 1;
        }

        if (card_type === '') {
            $('#card_type').siblings('.err').text('Please Select Card Type');
            hasError = 1;
        }

        if (card_number === '') {
            $('#card_number').siblings('.err').text('Card Number is required');
            hasError = 1;
        }

        if (card_type == 'Aadhaar Card') {

            if (!validateAadhar(card_number)) {
                $('#card_number').siblings('.err').text('Invalid Aadhaar Card Number');
                hasError = 1;
            }

            // Return 1 if error is found, otherwise return 0
            return hasError;
        }

        if (formType == 'add' && card_file === '') {
            $('#card_file').siblings('.err').text('Required');
            hasError = 1;
        }

        // Return 1 if error is found, otherwise return 0
        return hasError;
    }

    async function checkDuplicate(field, value, userId = null) {
        let url;
        let body;
        if (field === 'phone') {
            url = '{{ route($thisModule . '.check.user.phone') }}';
            body = {
                phone: value,
                userId: userId,
            };
        } else if (field === 'email') {
            url = '{{ route($thisModule . '.check.user.email') }}';
            body = {
                email: value,
                userId: userId,
            };
        }

        let headers = {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        };

        try {
            let response = await fetch(url, {
                method: 'POST',
                headers: headers,
                body: JSON.stringify(body)
            });

            let data = await response.json();
            if (data.success === true) {
                return true;
            } else {
                return false;
            }

        } catch (error) {
            console.error('Error checking duplicate:', error);
            return false;
        }
    }

    function validateEmail(email) {
        let emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    function validatePhone(phone) {
        let phoneRegex = /^\d{10}$/;
        return phoneRegex.test(phone);
    }

    function validateAadhar(aadhaar) {
        let phoneRegex = /^\d{12}$/;
        return phoneRegex.test(aadhaar);
    }
</script>
{{-- show edit form --}}
<script>
    $(document).ready(function () {
        $('body').on('click', '.edit', function () {
            $('.err').text('');
            // loader add
            $('#loader').css('width', '50%');
            $('#loader').fadeIn();
            $('#blockOverlay').fadeIn();

            // $('#addStaffForm')[0].reset();
            $('#addStaffForm').find(
                'input:not([name="_token"],[name^="created_by"]), select, textarea'
            ).each(
                function () {
                    $(this).val('');
                });

            $('#addStaffForm select').each(function () {
                $(this).prop('selectedIndex', 0); // Select the first option
            });
            // disable outside click + exc press
            $('#addStaffModal').modal({
                backdrop: 'static',
                keyboard: false
            })
            // change modal heading
            $('#modalHeadTxt').text('Edit Staff');
            $('#submitaddStaffForm').text('Update');

            const staffId = $(this).attr('id');

            $.ajax({
                url: "{{ route($thisModule . '.staff.edit', ['id' => ':staffId']) }}"
                    .replace(':staffId', staffId),
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                dataType: 'json',
                processData: false,
                contentType: false,
                success: function (res) {
                    data = res.data;
                    if (res.status == 'error') {
                        toastr[res.status](res.message);
                    }
                    $('#addStaffForm').attr('action',
                        '{{ route($thisModule . '.staff.update', ['id' => '__ID__']) }}'
                            .replace('__ID__', data.id));
                    $('#submitaddStaffForm').attr('data-formtype', 'edit');

                    //feed #addStaffForm form data by jq below
                    $('#id').val(data.id);
                    $('#daily_help_block').show();
                    $('#user_id').val(data.user_id);
                    $('#employee_id').val(data.employee_id);
                    $('#assigned_area').val(data.assigned_area);
                    $('#dob').val(data.dob);
                    if (data.daily_help == 1) {
                        $("#daily_help").prop("checked", true);
                    } else {
                        $("#daily_help").prop("checked", false);
                    }
                    $('#gender').val(data.gender);
                    if (data.shift_from && data.shift_from.length >= 5) {
                        let formattedshift_from = data.shift_from.substring(0, 5);
                        timePicker_shift_from.setDate(formattedshift_from, true);
                    }
                    if (data.shift_to && data.shift_to.length >= 5) {
                        let formattedshift_to = data.shift_to.substring(0, 5);
                        timePicker_shift_to.setDate(formattedshift_to, true);
                    }

                    if(data.off_days && data.off_days.length > 0) {

                        var offDaysArray = data.off_days.split(',');
                        $('#off_days').val(offDaysArray).trigger('change');
                    }
                    // Set the values to the select2 dropdown

                    $('#emer_name').val(data.emer_name);
                    $('#emer_relation').val(data.emer_relation);
                    $('#emer_phone').val(data.emer_phone);
                    $('#date_of_join').val(data.date_of_join);
                    $('#contract_end_date').val(data.contract_end_date);
                    let salary = data.monthly_salary;

                    // Check if salary is a valid number, otherwise set it to 0.00
                    if (isNaN(parseFloat(salary)) || !isFinite(salary)) {
                        salary = '0.00';
                    } else {
                        salary = parseFloat(salary).toFixed(2);
                    }

                    $('#monthly_salary').val(salary);
                    $('#society_id').val(data.society_id);
                    $('#name').val(data.name);
                    $('#role').val(data.role);

                    if (data.complaint_category_id != null && data.complaint_category_id !==
                        '') {
                        // console.log('len', data.complaint_category_id.toString().length);
                        if (data.complaint_category_id.toString().length > 0) {
                            // console.log(data.complaint_category_id);
                            $('#complaint_category_id').val(data.complaint_category_id);
                            $('#service_category_block').show();
                        }
                    }

                    $('#phone').val(data.phone);
                    $('#email').val(data.email);
                    $('#location').val(data.address);
                    $('#card_type').val(data.card_type);
                    $('#card_number').val(data.card_number);

                    //loader removed
                    $('#loader').css('width', '100%');
                    $('#loader').fadeOut();
                    $('#blockOverlay').fadeOut();

                    $('#addStaffModal').modal('show');
                },
                error: function (xhr, status, error) {

                    //loader removed
                    $('#loader').css('width', '100%');
                    $('#loader').fadeOut();
                    $('#blockOverlay').fadeOut();

                    toastr.error('Unable to process the data');
                    console.error('Unable to process the data', error);
                }
            });
        });
    });
</script>
{{-- delete submit --}}
<script>
    $(document).ready(function () {
        $('.delete').on('click', function () {
            var staffId = $(this).data('id');
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to undo this action!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route($thisModule . '.staff.delete', ['id' => ':staffId']) }}"
                            .replace(':staffId', staffId),
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        },
                        success: function (response) {
                            toastr[response.status](response.message);
                            setTimeout(function () {
                                location.reload();
                            }, 2000);
                        },
                        error: function (xhr) {
                            toastr.error('Failed please try again.');
                        }
                    });
                }
            });
        });
    });
</script>
{{-- status update --}}
<script>
    $(document).on('change', '.statusOption', function () {

        let $hiddenInput = $(this).parents('td').find('input[type=hidden]');

        let preSttsN = $hiddenInput.val();
        let nowStts = $(this).val();
        let $this = $(this);
        let preSttsS = '';
        if (nowStts == 'active') {
            preSttsS = 'inactive';
        } else {

            preSttsS = 'active';
        }


        $hiddenInput.val((+$hiddenInput.val() === 0) ? 1 : 0);
        console.log($hiddenInput.val());

        if ($hiddenInput.val() == 1) {
            var toStatus = 'active';
        } else {

            var toStatus = 'inactive';
        }
        console.log(toStatus);


        var staffId = $(this).data('id');
        Swal.fire({
            title: 'Are you sure ?',
            text: "You want to change the status",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, change it !',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route($thisModule . '.staff.status.change', ['id' => ':staffId', 'status' => ':toStatus']) }}"
                        .replace(':staffId', staffId).replace(':toStatus', toStatus),
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    success: function (response) {
                        toastr[response.status](response.message);
                        setTimeout(function () {
                            location.reload();
                        }, 2000);
                    },
                    error: function (xhr) {
                        toastr.error('Failed please try again.');
                    }
                });
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                // When the cancel button is clicked
                $this.val(preSttsS); // Reset the status option
                $hiddenInput.val(preSttsN); // Reset the hidden input value
            }
        });
    });
</script>
<script>
    $(document).ready(function () {
        // Get the current year
        var currentYear = new Date().getFullYear();

        // Calculate the allowed max year (current year - 15)
        var restrictedYear = currentYear - 15;

        // Set the max date as December 31st of the restricted year
        var maxDate = restrictedYear + "-12-31";

        // Apply the max attribute to restrict selection
        $('#dob').attr('max', maxDate);
    });
</script>

<script>
    function checkShiftOverlap(selectedMember, newFrom, newTo) {
    let isOverlapping = false;

    $(".member-shift-row").each(function () {
        let member = $(this).find(".select-member").val();
        let shiftFrom = $(this).find("input[name='m_shift_from[]']").val();
        let shiftTo = $(this).find("input[name='m_shift_to[]']").val();

        // Skip checking for the newly added row itself
        if (!shiftFrom || !shiftTo || !member || member !== selectedMember) return;

        if ((newFrom >= shiftFrom && newFrom < shiftTo) ||
            (newTo > shiftFrom && newTo <= shiftTo) ||
            (newFrom <= shiftFrom && newTo >= shiftTo)) {
            isOverlapping = true;
        }
    });

    return isOverlapping;
}

    $(document).ready(function () {

    function initializeSelect2() {
        $('.select-member').select2({
            width: '100%',
            dropdownParent: $('#assignMember'),
            minimumResultsForSearch: 0
        });
    }

    function updateSelectOptionsold() {
        let selectedValues = $(".select-member").map(function () {
            return $(this).val();
        }).get().filter(value => value !== ""); // Get all selected values excluding empty

        $(".select-member").each(function () {
            let $this = $(this);
            let currentValue = $this.val();

            // Re-enable all options first
            $this.find("option").prop("disabled", false);

            // Disable options that are selected in other rows
            $this.find("option").each(function () {
                let optionValue = $(this).val();
                if (optionValue && selectedValues.includes(optionValue) && optionValue !== currentValue) {
                    $(this).prop("disabled", true);
                }
            });
        });
    }

    function updateSelectOptions() {
    let selectedValues = $(".select-member").map(function () {
        return $(this).val();
    }).get().filter(value => value !== ""); // Get all selected values excluding empty

    $(".select-member").each(function () {
        let $this = $(this);
        let currentValue = $this.val();

        // Re-enable all options first
        $this.find("option").prop("disabled", false);

        // Disable options that are selected in other rows
        $this.find("option").each(function () {
            let optionValue = $(this).val();
            if (optionValue && selectedValues.includes(optionValue) && optionValue !== currentValue) {
                $(this).prop("disabled", true);
            }
        });
    });

    // Check for overlapping shift times across all members
    let shifts = [];
    let hasOverlap = false;

    $(".member-shift-row").each(function () {
        let memberId = $(this).find(".select-member").val();
        let shiftFrom = $(this).find("input[name='m_shift_from[]']").val();
        let shiftTo = $(this).find("input[name='m_shift_to[]']").val();

        if (memberId && shiftFrom && shiftTo) {
            let shiftFromTime = convertToMinutes(shiftFrom);
            let shiftToTime = convertToMinutes(shiftTo);

            // Validate shift overlaps across all members
            for (let shift of shifts) {
                let existingFrom = shift.shiftFromTime;
                let existingTo = shift.shiftToTime;

                if (
                    (shiftFromTime < existingTo && shiftToTime > existingFrom) // Overlap condition
                ) {
                    hasOverlap = true;
                    break;
                }
            }

            shifts.push({ memberId, shiftFromTime, shiftToTime });
        }
    });

    if (hasOverlap) {
        Swal.fire("Error!", "Shift timings should not overlap across members.", "error");
    }
}




    // Initialize Select2 for existing elements
    initializeSelect2();
    updateSelectOptions();

    $(document).on("click", ".add-row-old", function () {
        let newRow = `<div class="row mb-2 member-shift-row">
            <div class="col-md-4">
                <select name="user_id[]" class="form-select form-control select-member">
                    <option value="">--select--</option>
                    @if (!empty($societyResidents))
                        @foreach ($societyResidents as $resident)
                            <option value="{{ $resident->user_id }}">
                                {{ $resident->name }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div class="col-md-3">
                <input type="time" name="m_shift_from[]" class="form-control">
            </div>
            <div class="col-md-3">
                <input type="time" name="m_shift_to[]" class="form-control">
            </div>
            <div class="col-md-2 d-flex align-items-end" style="padding-bottom: 12px">
                <button type="button" class="btn btn-danger remove-row">-</button>
            </div>
        </div>`;

        $("#memberShiftContainer").append(newRow);

        // Initialize Select2 for the new element
        $("#memberShiftContainer .member-shift-row:last-child .select-member").select2({
            width: '100%',
            dropdownParent: $('#assignMember'),
            minimumResultsForSearch: 0
        });

        updateSelectOptions();
    });

    $(document).on("click", ".add-row", function () {

        // Check if no row exists
    if ($(".member-shift-row").length > 0) {
        // Handle first-row-specific logic here if needed
        let lastRow = $(".member-shift-row:last");
        let member = lastRow.find(".select-member");
        let shiftFrom = lastRow.find("input[name='m_shift_from[]']");
        let shiftTo = lastRow.find("input[name='m_shift_to[]']");
        let errorSpan = lastRow.find(".err");
        let isValid = true;

        // Check if member is selected
        if (!member.val()) {
            errorSpan.eq(0).text("Required");
            isValid = false;
        } else {
            errorSpan.eq(0).text(""); // Remove error message
        }
        // Check if shift times are filled
        if (!shiftFrom.val()) {
            setTimeout(function () {
                errorSpan.eq(1).text("Required");
            }, 50);

            isValid = false;
        } else {
            setTimeout(function () {
                errorSpan.eq(1).text(""); // Remove error message
            }, 50);
        }
        if (!shiftTo.val()) {
            setTimeout(function () {
                errorSpan.eq(2).text("Required");
            }, 50);
            isValid = false;
        } else {
            setTimeout(function () {
                errorSpan.eq(2).text(""); // Remove error message
            }, 50);
        }
        if (!isValid) return;
        if (shiftTo.val() <=  shiftFrom.val()) {
            Swal.fire("Error!", "Shift From should not greater than or equal with Shift To time.", "error");
            return ;
        }

        // Check for overlapping shift times across all members
        let shifts = [];
        let hasOverlap = false;
        $(".member-shift-row").each(function () {
            let memberId = $(this).find(".select-member").val();
            let shiftFrom = $(this).find("input[name='m_shift_from[]']").val();
            let shiftTo = $(this).find("input[name='m_shift_to[]']").val();

            if (memberId && shiftFrom && shiftTo) {
                let shiftFromTime = convertToMinutes(shiftFrom);
                let shiftToTime = convertToMinutes(shiftTo);

                // Validate shift overlaps across all members
                for (let shift of shifts) {
                    let existingFrom = shift.shiftFromTime;
                    let existingTo = shift.shiftToTime;

                    if (
                        (shiftFromTime < existingTo && shiftToTime > existingFrom) // Overlap condition
                    ) {
                        hasOverlap = true;
                        break;
                    }
                }

                shifts.push({ memberId, shiftFromTime, shiftToTime });
            }
        });
        if (hasOverlap) {
            Swal.fire("Error!", "Shift timings should not overlap across members.", "error");
            return ;
        }

        // Stop adding new row if validation fails
        if (!isValid) return;
    }
    // Add new row if all validations pass
    let newRow = `<div class="row mb-2 member-shift-row">
        <div class="col-md-4">
            <select name="user_id[]" class="form-select form-control select-member">
                <option value="">--select--</option>
                @if (!empty($societyResidents))
                    @foreach ($societyResidents as $resident)
                        <option value="{{ $resident->user_id }}">
                            {{ $resident->name }}
                        </option>
                    @endforeach
                @endif
            </select>
            <span class="text-danger err"></span>
        </div>
        <div class="col-md-3">
            <input type="time" name="m_shift_from[]" class="form-control">
            <span class="text-danger err"></span>
        </div>
        <div class="col-md-3">
            <input type="time" name="m_shift_to[]" class="form-control">
            <span class="text-danger err"></span>
        </div>
        <div class="col-md-2 d-flex align-items-end" style="padding-bottom: 34px">
            <button type="button" class="btn btn-danger remove-row">-</button>
        </div>
    </div>`;
    $("#memberShiftContainer").append(newRow);
    // Initialize Select2 for the new row
    $("#memberShiftContainer .member-shift-row:last-child .select-member").select2({
        width: '100%',
        dropdownParent: $('#assignMember'),
        minimumResultsForSearch: 0
    });
    updateSelectOptions();
});

    // Restrict selection change after first selection
    $(document).on("change", ".select-member", function () {
        let $this = $(this);
        if ($this.data("locked")) {
            return; // Already locked, do nothing
        }
        $this.data("locked", true); // Lock the selection
        updateSelectOptions();
    });


    // Prevent clicking on locked dropdowns
    $(document).on("select2:open", function (e) {
        let $target = $(e.target);
        if ($target.hasClass("select-member") && $target.data("locked")) {
            $target.select2("close"); // Close select2 if locked
        }
    });

    // Remove row and update available options
    $(document).on("click", ".remove-row", function () {
        let row = $(this).closest(".member-shift-row");

        Swal.fire({
            title: "Are you sure?",
            text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Yes, delete it!"
        }).then((result) => {
            if (result.isConfirmed) {
                row.remove();

                updateSelectOptions();
                Swal.fire({
                title: "Deleted!",
                text: "The row has been removed.",
                icon: "success",
                showConfirmButton: false, // Hide "OK" button
                timer: 1500 // Auto-close after 1.5 seconds
            });
            }
        });
    });
});

// $(document).on('click', '[data-bs-target="#assignMember"]', function () {
//     let staffUserId = $(this).data("user-id"); // Get user ID from clicked link
//     $('#staff_user_id').val(staffUserId); // Store in modal if needed
// });

$(document).on('click', '[data-bs-target="#assignMember"]', function () {
    let staffUserId = $(this).data("user-id");
    $('#staff_user_id').val(staffUserId);

    // Fetch assigned members
    $.ajax({
        url: "{{ route($thisModule . '.staff.getAssignedMembers', ['staffUserId' => ':staffId']) }}".replace(':staffId', staffUserId),
        type: "GET",
        success: function(response) {
            if (response.success) {
                let data = response.data;
                let unassignedResidents = response.unassigned_residents;
                let selectedMembers = data.map(member => member.member_user_id); // Store selected user IDs
                // let html = '';
                let firstRow = true; // Track first row to show labels only once
                let html = `
                <div class="row mb-4">
                    <input type="hidden" name="staff_user_id" id="staff_user_id" value="${staffUserId}">
                    <div class="col-md-4"><label class="form-label">Choose Member</label></div>
                    <div class="col-md-3"><label class="form-label">Shift From</label></div>
                    <div class="col-md-3"><label class="form-label">Shift To</label></div>
                    <div class="col-md-2"><button type="button" class="btn btn-primary add-row">+</button></div>
                </div>`;

                if (data.length > 0) {
                    data.forEach(member => {
                        html += `
                            <div class="row mb-2 member-shift-row">
                                <div class="col-md-4">
                                    <input type="hidden" name="staff_user_id" id="staff_user_id" value="${staffUserId}">
                                    <select name="user_id[]" class="select-member form-select form-control">
                                        <option value="${member.member_user_id}" selected>${member.name}</option>
                                    </select>
                                    <span class="text-danger err"></span>
                                </div>
                                <div class="col-md-3">
                                    <input type="time" name="m_shift_from[]" class="form-control" value="${member.shift_from}">
                                    <span class="text-danger err"></span>
                                </div>
                                <div class="col-md-3">
                                    <input type="time" name="m_shift_to[]" class="form-control" value="${member.shift_to}">
                                    <span class="text-danger err"></span>
                                </div>
                                <div class="col-md-2 d-flex align-items-end" style="padding-bottom: 12px">
                                    <button type="button" class="btn btn-danger remove-row">-</button>
                                </div>
                            </div>`;
                        firstRow = false; // Labels will not appear after first row
                    });
                }
                console.log(selectedMembers);

                // // Append the new row at the end
                // html += `
                //     <div class="row mb-2 member-shift-row">
                //         <div class="col-md-4">
                //             <select name="user_id[]" class="select-member form-select form-control">
                //                 <option value="">--select--</option>`;
                //                 unassignedResidents.forEach(resident => {
                //                     html += `<option value="${resident.user_id}">${resident.name}</option>`;
                //                 });
                //                 html += `
                //             </select>
                //             <span class="text-danger err"></span>
                //         </div>
                //         <div class="col-md-3">
                //             <input type="time" name="m_shift_from[]" class="form-control">
                //             <span class="text-danger err"></span>
                //         </div>
                //         <div class="col-md-3">
                //             <input type="time" name="m_shift_to[]" class="form-control">
                //             <span class="text-danger err"></span>
                //         </div>
                //         <div class="col-md-2 d-flex align-items-end" style="padding-bottom: 12px">
                //             <button type="button" class="btn btn-primary add-row">+</button>
                //         </div>
                //     </div>`;

                $("#memberShiftContainer").html(html);
                initializeSelect2(); // Call Select2 initialization
            }
        }
    });

    function initializeSelect2() {
        $('.select-member').select2({
            width: '100%',
            dropdownParent: $('#assignMember'),
            minimumResultsForSearch: 0
        });
    }
});
$(document).ready(function () {
    $("#assignMemberFormBtn").on("click", function (e) {
        e.preventDefault(); // Prevent default button action
        if ($(".member-shift-row").length > 0) {
        // Handle first-row-specific logic here if needed
        let lastRow = $(".member-shift-row:last");
        let member = lastRow.find(".select-member");
        let shiftFrom = lastRow.find("input[name='m_shift_from[]']");
        let shiftTo = lastRow.find("input[name='m_shift_to[]']");
        let errorSpan = lastRow.find(".err");
        let isValid = true;

        // Check if member is selected
        if (!member.val()) {
            errorSpan.eq(0).text("Required");
            isValid = false;
        } else {
            errorSpan.eq(0).text(""); // Remove error message
        }
        // Check if shift times are filled
        if (!shiftFrom.val()) {
            setTimeout(function () {
                errorSpan.eq(1).text("Required");
            }, 50);

            isValid = false;
        } else {
            setTimeout(function () {
                errorSpan.eq(1).text(""); // Remove error message
            }, 50);
        }
        if (!shiftTo.val()) {
            setTimeout(function () {
                errorSpan.eq(2).text("Required");
            }, 50);
            isValid = false;
        } else {
            setTimeout(function () {
                errorSpan.eq(2).text(""); // Remove error message
            }, 50);
        }
        if (!isValid) return;
        if (shiftTo.val() <=  shiftFrom.val()) {
            Swal.fire("Error!", "Shift From should not greater than or equal with Shift To time.", "error");
            return ;
        }

        // Check for overlapping shift times across all members
        let shifts = [];
        let hasOverlap = false;
        $(".member-shift-row").each(function () {
            let memberId = $(this).find(".select-member").val();
            let shiftFrom = $(this).find("input[name='m_shift_from[]']").val();
            let shiftTo = $(this).find("input[name='m_shift_to[]']").val();

            if (memberId && shiftFrom && shiftTo) {
                let shiftFromTime = convertToMinutes(shiftFrom);
                let shiftToTime = convertToMinutes(shiftTo);

                // Validate shift overlaps across all members
                for (let shift of shifts) {
                    let existingFrom = shift.shiftFromTime;
                    let existingTo = shift.shiftToTime;

                    if (
                        (shiftFromTime < existingTo && shiftToTime > existingFrom) // Overlap condition
                    ) {
                        hasOverlap = true;
                        break;
                    }
                }

                shifts.push({ memberId, shiftFromTime, shiftToTime });
            }
        });
        if (hasOverlap) {
            Swal.fire("Error!", "Shift timings should not overlap across members.", "error");
            return ;
        }

        // Stop adding new row if validation fails
        if (!isValid) return;
    }
        $("#assignMemberForm").submit();
    });
});
// Helper function to convert time to minutes
function convertToMinutes(time) {
    let [hours, minutes] = time.split(":").map(Number);
    return hours * 60 + minutes;
}
</script>
<script>
    function validateFile(input) {
        // alert('select');
        const maxSize = 10 * 1024 * 1024; // 10MB
        const allowedExtensions = ['jpg', 'jpeg', 'png'];

        if (input.files.length > 0) {
            const file = input.files[0];
            const fileSize = file.size;
            const fileExtension = file.name.split('.').pop().toLowerCase();

            // Check file extension
            if (!allowedExtensions.includes(fileExtension)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid File Type!',
                    text: 'Only JPG, JPEG, and PNG files are allowed.',
                    timer: 3000,
                    showConfirmButton: false
                });
                input.value = ""; // Clear input
                return;
            }

            // Check file size
            if (fileSize > maxSize) {
                Swal.fire({
                    icon: 'error',
                    title: 'File Too Large!',
                    text: 'Please select a file smaller than 10MB.',
                    timer: 3000,
                    showConfirmButton: false
                });
                input.value = ""; // Clear input
            }
        }
    }
    </script>

@endpush
