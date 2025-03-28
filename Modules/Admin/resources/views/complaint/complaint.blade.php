@extends($thisModule . '::layouts.master')

@section('title', 'Complaints')

@section('content')
    <div class="right_main_body_content members_page">
        <div class="head_content">
            <div class="left_head">
                {{-- <h2>Complaint Board</h2> --}}
                {{-- <p>Add or manage complaints of the society</p> --}}
            </div>
            <!-- Button trigger modal -->

        </div>
        <div class="custom_table_wrapper">
            <div class="filter_table_head">
                <div class="search_wrapper search-members-gstr">
                    <form method="GET">
                        <div class="input-group">
                            <div class="filter-box">
                                <div class="filter-secl">
                                    <label for="category">Category</label>
                                    <select name="category" id="category" class=" form-select form-control">
                                        <option value="">All</option>
                                        @foreach ($complaintCategories as $category)
                                            <option value="{{ $category->id }}"
                                                {{ request('category') == $category->id ? 'selected' : '' }}
                                                >{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="filter-secl">
                                    <label for="status">Status</label>
                                    <select name="status" id="status" class="form-select form-control">
                                        <option value="">All</option>
                                        <option value="requested"
                                        {{ request('status') == 'requested' ? 'selected' : '' }}>New</option>
                                        <option value="assigned"
                                        {{ request('status') == 'assigned' ? 'selected' : '' }}>Assigned</option>
                                        <option value="in_progress"
                                        {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                        <option value="done"
                                        {{ request('status') == 'done' ? 'selected' : '' }}>Resolved</option>
                                        <option value="cancelled"
                                        {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                </div>

                            </div>
                            <div class="search-full-box">
                                <input type="search" name="search" id="search" placeholder="Search.."
                                    value="{{ request('search') }}">
                                <button type="submit" class="bg_theme_btn">
                                    Filter
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="right_filters">
                    {{-- <h2>Complaint Listing </h2> --}}
                    {{-- <button class="sortby">
                        <svg width="25" height="24" viewBox="0 0 25 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path d="M3.13306 7H21.1331" stroke="#020015" stroke-width="1.5" stroke-linecap="round" />
                            <path d="M6.13306 12H18.1331" stroke="#020015" stroke-width="1.5" stroke-linecap="round" />
                            <path d="M10.1331 17H14.1331" stroke="#020015" stroke-width="1.5" stroke-linecap="round" />
                        </svg>
                        Sort By
                    </button>
                    <button class="filterbtn">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
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
                            <th class="text-center">Property Number</th>
                            <th class="text-center">Tower</th>
                            <th class="text-center">Floor</th>
                            <th>Complaint By</th>
                            <th>Category</th>
                            <th class="text-center">Area</th>
                            {{-- <th>Description</th> --}}
                            <th class="text-center">Complaint Date</th>
                            <th>Assigned To</th>
                            <th class="text-center">status</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $sl = 0;
                        @endphp
                        @if ($complaints && !$complaints->isEmpty())
                            @foreach ($complaints as $complaint)
                                @php
                                    $sl++;
                                    // 'requested','assigned','in_progress','done','cancelled'

                                    if ($complaint->status == 'requested') {
                                        $stts_text = 'New';
                                        $stts_class = 'up-status-btn';
                                    } elseif ($complaint->status == 'assigned') {
                                        $stts_text = 'Assigned';
                                        $stts_class = 'grey-status-btn';
                                    } elseif ($complaint->status == 'in_progress') {
                                        $stts_text = 'In Progress';
                                        $stts_class = 'yellow-status-btn';
                                    } elseif ($complaint->status == 'done') {
                                        $stts_text = 'Resolved';
                                        $stts_class = 'green-status-btn';
                                    } elseif ($complaint->status == 'cancelled') {
                                        $stts_text = 'Cancelled';
                                        $stts_class = 'exp-status-btn';
                                    }
                                @endphp
                                <tr>
                                    <td class="text-center">{{ $complaint->aprt_no }}</td>
                                    <td class="text-center">{{ $complaint->block_name }}</td>
                                    <td class="text-center">{{ $complaint->floor_number }}</td>
                                    <td class="text-center">{{ $complaint->complaintBy->name }}</td>
                                    <td>{{ $complaint->serviceCategory->name }}</td>
                                    <td class="text-center">{{ $complaint->area }}</td>

                                    <td class="text-center">
                                        {{ \Carbon\Carbon::parse($complaint->complaint_at)->format('d M Y g:i A') }}</td>
                                    <td>
                                        @php
                                            $staffList = $complaint->staff;
                                        @endphp
                                        @can('complaints.assign')
                                            <select name="staff_user_id" data-complaint-id="{{ $complaint->id }}"
                                                id="assignServiceProvider" class="form-select">
                                                <option value="" selected>--Select--</option>
                                                @foreach ($staffList as $staff)
                                                    @if ($staff->user_id == $complaint->assigned_to)
                                                        <option value="{{ $staff->user_id }}" selected>{{ $staff->name }}
                                                        </option>
                                                    @else
                                                        <option value="{{ $staff->user_id }}">{{ $staff->name }}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        @endcan
                                        @cannot('complaints.assign')
                                            {{ !empty($complaint->assignedTo->name) ? $complaint->assignedTo->name : '' }}
                                        @endcannot

                                    </td>
                                    <td class="text-center {{ $stts_class }}">
                                        <button class="text-uppercase">{{ $stts_text }}</button>
                                    </td>
                                    <td class="text-center">
                                        <div class="actions">
                                            <a href="{{ route($thisModule . '.complaint.details', ['id' => $complaint->id]) }}"
                                                id="{{ $complaint->id }}">
                                                <svg width="15" height="15" viewBox="0 0 15 15" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M0.625 7.5C0.625 7.5 3.125 2.5 7.5 2.5C11.875 2.5 14.375 7.5 14.375 7.5C14.375 7.5 11.875 12.5 7.5 12.5C3.125 12.5 0.625 7.5 0.625 7.5Z"
                                                        stroke="#8077F5" stroke-width="1.25" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                    <path
                                                        d="M7.5 9.375C8.53553 9.375 9.375 8.53553 9.375 7.5C9.375 6.46447 8.53553 5.625 7.5 5.625C6.46447 5.625 5.625 6.46447 5.625 7.5C5.625 8.53553 6.46447 9.375 7.5 9.375Z"
                                                        stroke="#8077F5" stroke-width="1.25" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                </svg>

                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="10" class="text-center"> No Data Found </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
                <div class="table_bottom_box">
                    {{-- Pagination Links --}}
                    <div class="d-flex justify-content-between p-2 mt-2 mb-2">
                        <div>
                            Showing {{ $complaints->firstItem() }} to {{ $complaints->lastItem() }} of
                            {{ $complaints->total() }} results
                        </div>
                        <div>
                            {{ $complaints->links('vendor.pagination.bootstrap-5') }} {{-- Bootstrap 5 pagination view --}}
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

@endsection

@push('footer-script')
    <script>
        $(document).ready(function() {

            $('.residentList').select2({

                width: '100%',
                dropdownParent: $('.filter-box'),
                minimumResultsForSearch: 0 // Always show the search box
            });

            $('table').on('change', '#assignServiceProvider', function() {

                let staffUserId = $(this).val();
                let complaintId = $(this).data('complaint-id');
                // check staffUserId numeric and > 0
                if (isNaN(staffUserId) || staffUserId <= 0) {
                    return;
                }

                Swal.fire({
                    title: 'Are you sure ?',
                    text: "You want to assign this Maintenance Staff",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, assign !',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route($thisModule . '.complaint.assign.serviceProvider') }}",
                            method: 'POST',
                            data: {
                                _token: csrfToken,
                                staff_user_id: staffUserId,
                                complaint_id: complaintId
                            },
                            success: function(response) {
                                toastr[response.status](response.message);
                                setTimeout(function() {
                                    location.reload();
                                }, 2000);
                            },
                            error: function(xhr) {
                                toastr.error('Failed please try again.');
                                setTimeout(function() {
                                    location.reload();
                                }, 2000);
                            }
                        });
                    } else if (result.dismiss === Swal.DismissReason.cancel) {
                        setTimeout(function() {
                            location.reload();
                        }, 2000);
                    }
                });

            });
        });
    </script>
@endpush
