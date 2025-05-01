@extends($thisModule . '::layouts.master')

@section('title', 'Staff')

@section('content')
    <div class="right_main_body_content society_detail_page">
        <div class="head_content">
            <div class="left_head">
                <h2>
                    <a href="javascript:void(0)" onclick="history.back()">
                        <svg width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M5.25 11.7092H20.25C20.4489 11.7092 20.6397 11.7882 20.7803 11.9289C20.921 12.0696 21 12.2603 21 12.4592C21 12.6581 20.921 12.8489 20.7803 12.9896C20.6397 13.1302 20.4489 13.2092 20.25 13.2092H5.25C5.05109 13.2092 4.86032 13.1302 4.71967 12.9896C4.57902 12.8489 4.5 12.6581 4.5 12.4592C4.5 12.2603 4.57902 12.0696 4.71967 11.9289C4.86032 11.7882 5.05109 11.7092 5.25 11.7092Z"
                                fill="black" />
                            <path
                                d="M5.56086 12.4592L11.7814 18.6782C11.9222 18.819 12.0013 19.01 12.0013 19.2092C12.0013 19.4084 11.9222 19.5994 11.7814 19.7402C11.6405 19.881 11.4495 19.9601 11.2504 19.9601C11.0512 19.9601 10.8602 19.881 10.7194 19.7402L3.96936 12.9902C3.89952 12.9205 3.84411 12.8378 3.8063 12.7466C3.76849 12.6555 3.74902 12.5578 3.74902 12.4592C3.74902 12.3605 3.76849 12.2629 3.8063 12.1717C3.84411 12.0806 3.89952 11.9979 3.96936 11.9282L10.7194 5.1782C10.8602 5.03737 11.0512 4.95825 11.2504 4.95825C11.4495 4.95825 11.6405 5.03737 11.7814 5.1782C11.9222 5.31903 12.0013 5.51004 12.0013 5.7092C12.0013 5.90836 11.9222 6.09937 11.7814 6.2402L5.56086 12.4592Z"
                                fill="black" />
                        </svg>
                        Details
                    </a>
                </h2>
            </div>
        </div>
        <div class="bg-card">
            <div class="society_details">
                <ul>
                    <li>
                        <p>Society</p>
                        <h4>{{ $staff->society->name }}</h4>
                    </li>
                    <li>
                        <p>Name </p>
                        <h4>{{ $staff->name }}</h4>
                    </li>
                    <li>
                        @php
                            $role = '';
                            if ($staff->role == 'staff') {
                                $role = 'Maintenance Staff';
                            } elseif ($staff->role == 'staff_security_guard') {
                                $role = 'Security Guard';
                            }else{
                                $role = ucwords(str_replace('_',' ',str_replace('staff', '', $staff->role)));
                            }
                        @endphp
                        <p>Role </p>
                        <h4>{{ $role }}</h4>
                    </li>
                    @if ($staff->role == 'staff')
                        <li>
                            <p>Category </p>
                            <h4>{{ $staff->staffCategory->name }}</h4>
                        </li>
                    @endif
                    <li>
                        <p>Employee Id </p>
                        <h4>{{ $staff->employee_id ? $staff->employee_id : '--' }}</h4>
                    </li>
                    <li>
                        <p>Assigned Area </p>
                        <h4>{{ $staff->assigned_area ? $staff->assigned_area : '--' }}</h4>
                    </li>
                    <li>
                        <p>Gender </p>
                        <h4>{{ $staff->gender ? ucfirst($staff->gender) : '--' }}</h4>
                    </li>
                    <li>
                        <p>Dob </p>
                        <h4>{{ $staff->dob ? date('d M Y',strtotime($staff->dob)) : '--' }}</h4>
                    </li>
                    <li>
                        <p>Phone </p>
                        <h4>{{ $staff->phone ? $staff->phone : '--' }}</h4>
                    </li>
                    <li>
                        <p>Email </p>
                        <h4>{{ $staff->email ? $staff->email : '--' }}</h4>
                    </li>
                    <li>
                        <p>Address </p>
                        <h4>{{ $staff->address ? $staff->address : '--' }}</h4>
                    </li>
                    <li>
                        <p>Status </p>
                        <h4>{{ $staff->status ? Str::ucfirst(str_replace('_','',$staff->status)) : '--' }}</h4>
                    </li>
                </ul>
                <p class="text-primary pb-2">Shift Details</p>
                <ul>
                    <li>
                        <p>Shift From </p>
                        <h4>
                            {{ $staff->shift_from ? date('h:i A',strtotime($staff->shift_from)) : '--' }}
                        </h4>
                    </li>
                    <li>
                        <p>Shift From </p>
                        <h4>
                            {{ $staff->shift_to ? date('h:i A',strtotime($staff->shift_to)) : '--' }}
                        </h4>
                    </li>
                    <li>
                        <p>Off Days </p>
                        <h4>{{ $staff->off_days ? $staff->off_days : '--' }}</h4>
                    </li>
                </ul>
                <p class="text-primary pb-2">Emergency Contact</p>
                <ul>
                    <li>
                        <p>Name</p>
                        <h4>{{ $staff->emer_name ? $staff->emer_name : '--' }}</h4>
                    </li>
                    <li>
                        <p>Relation</p>
                        <h4>{{ $staff->emer_relation ? $staff->emer_relation : '--' }}</h4>
                    </li>
                    <li>
                        <p>Phone</p>
                        <h4>{{ $staff->emer_phone ? $staff->emer_phone : '--' }}</h4>
                    </li>
                </ul>
                <p class="text-primary pb-2">Employment Details</p>
                <ul>
                    <li>
                        <p>Date Of Join</p>
                        <h4>{{ $staff->date_of_join ? date('d M Y', strtotime($staff->date_of_join)) : '--' }}</h4>
                    </li>
                    <li>
                        <p>Contract End Date</p>
                        <h4>{{ $staff->contract_end_date ? date('d M Y', strtotime($staff->contract_end_date)) : '--' }}</h4>
                    </li>
                    <li>
                        <p>Monthly Salary</p>
                        <h4>{{ $staff->monthly_salary != '0.00' ? $staff->monthly_salary : '--' }}</h4>
                    </li>
                    <li>
                        <p>Card Type </p>
                        <h4>{{ $staff->card_type ? $staff->card_type : '--' }}</h4>
                    </li>
                    <li>
                        <p>Card No </p>
                        <h4>{{ $staff->card_number ? $staff->card_number : '--' }}</h4>
                    </li>
                    <li>
                        <p>Card File </p>
                        <h4>
                            {{-- <div class="text-center d-grid m-2"> --}}

                                <a href="{{ $staff->card_file }}" target="_blank" class="">
                                    <img src="{{ $staff->card_file }}" width="100%" class="border border-3 rounded image-fit" alt="Image">
                                </a>
                            {{-- </div> --}}
                        </h4>
                    </li>
                </ul>
            </div>
        </div>

        @if ($dailyHelpStaffs->isNotEmpty())
            <div class="mt-4 bg-card">
                <div class="society_details">
                    <h4 class="text-primary">Assigned To Members</h4>

                    <table class="table table-responsive table-hover" width="100%" cellpadding="0" cellspacing="0">
                        <thead>
                            <tr>
                                <th class="text-center">Name</th>
                                <th class="text-center">Phone</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($dailyHelpStaffs as $dailyHelpStaff)
                                <tr>
                                    <td class="text-center">{{ $dailyHelpStaff->memberUser->name }} </td>
                                    <td class="text-center">{{ $dailyHelpStaff->memberUser->phone }} </td>
                                </tr>
                            @endforeach

                        </tbody>
                    </table>

                </div>
            </div>


            <div class="mt-4 bg-card">
                <div class="society_details">
                    <h4 class="text-primary">CheckIn / CheckOut Details</h4>
                    <br>
                    <div class="" style="width: 100%;">
                        <table class="table table-responsive table-hover" width="100%" cellpadding="0" cellspacing="0">
                            <thead>
                                <tr>
                                    <th class="text-center">Check In At</th>
                                    <th class="text-center">Check In By</th>
                                    <th class="text-center">Check Out At</th>
                                    <th class="text-center">Check Out By</th>
                                    <th class="text-center">CheckIn/CheckOut By</th>
                                    <th class="text-center">Member Info</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($checkin_logs->isNotEmpty())
                                    @foreach ($checkin_logs as $checkinDetail)
                                        <tr>
                                            <td class="text-center">
                                                {{ \Carbon\Carbon::parse($checkinDetail->checkin_at)->format('d M Y g:i A') }}
                                            </td>
                                            <td class="text-center">
                                                {{ $checkinDetail->checkedInBy->name ?? '' }}
                                            </td>
                                            <td class="text-center">
                                                {{ \Carbon\Carbon::parse($checkinDetail->checkout_at)->format('d M Y g:i A') }}
                                            </td>
                                            <td class="text-center">
                                                {{ $checkinDetail->checkedOutBy->name ?? '' }}
                                            </td>
                                            <td class="text-center">
                                                @if (in_array($checkinDetail->status,['checked_in','checked_out']))
                                                Security Guard
                                                @else
                                                Resident
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if (!empty($checkinDetail->dailyHelpMemberDetails->member))
                                                {{
                                                $checkinDetail->dailyHelpMemberDetails->member->name." , Tower : ".$checkinDetail->dailyHelpMemberDetails->member->block_name.", Property Number : ".$checkinDetail->dailyHelpMemberDetails->member->aprt_no}}
                                                @else
                                                -
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="5" class="text-center"> No Data Found</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                        @if ($checkin_logs->isNotEmpty())
                            <div class="table_bottom_box">
                                {{-- Pagination Links --}}
                                <div class="d-flex justify-content-between p-2 mt-2 mb-2">
                                    <div>
                                        Showing {{ $checkin_logs->firstItem() }} to
                                        {{ $checkin_logs->lastItem() }} of
                                        {{ $checkin_logs->total() }} results
                                    </div>
                                    <div>
                                        {{ $checkin_logs->links('vendor.pagination.bootstrap-5') }}
                                        {{-- Bootstrap 5 pagination view --}}
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@push('footer-script')
@endpush
