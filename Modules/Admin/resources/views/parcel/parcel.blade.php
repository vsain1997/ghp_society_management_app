@extends($thisModule . '::layouts.master')

@section('title', 'Parcels')

@section('content')
    <div class="right_main_body_content members_page ">

        <div class="custom_table_wrapper">
            <div class="filter_table_head visitor-sec">

                <div class="search_wrapper search-members-gstr">
                    <form method="GET" id="searchForm">

                        <div class="input-group">
                            <div class="filter-box">
                                <div class="filter-secl">
                                    <label for="user_id">Select Resident</label>
                                    <select name="user_id" id="user_id" class="residentList form-select form-control">
                                        <option value="">--select--</option>
                                        @if (!empty($societyResidents))
                                            @foreach ($societyResidents as $resident)
                                                <option data-floor="{{ $resident->floor_number }}"
                                                    data-block="{{ $resident->block_name }}"
                                                    data-unitType="{{ $resident->unit_type }}"
                                                    data-aprtNo="{{ $resident->aprt_no }}"
                                                    data-phone="{{ $resident->phone }}" data-name="{{ $resident->name }}"
                                                    value="{{ $resident->user_id }}"
                                                    @if (request('user_id') == $resident->user_id) selected @endif>
                                                    {{ $resident->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <span class="text-primary err" id="aprtNoShowData"></span>
                                </div>
                                <div class="filter-secl">

                                    <label for="request_type">Status</label>
                                    <select name="status" class="form-control">
                                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>
                                            Pending
                                        </option>
                                        <option value="received" {{ request('status') == 'received' ? 'selected' : '' }}>
                                            Received
                                        </option>
                                        <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>
                                            Delivered
                                        </option>
                                    </select>
                                </div>
                                <div class="filter-secl">
                                    <label for="request_type">From Date</label>
                                    <input type="date" class="form-control" id="from_date" name="from_date"
                                        value="{{ request('from_date') }}">
                                    <span class="text-danger" id="from_date_error"></span>
                                </div>
                                <div class="filter-secl">
                                    <label for="request_type">To Date</label>
                                    <input type="date" class="form-control" id="to_date" name="to_date"
                                        value="{{ request('to_date') }}">
                                    <span class="text-danger" id="to_date_error"></span>
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
                    {{-- <h2>Parcels </h2> --}}
                </div>
            </div>
            <div class="table-responsive visitors-table">
                <table width="100%" cellpadding="0" cellspacing="0">
                    <thead>
                        <tr>
                            <th class="text-center">Parcel Id</th>
                            <th class="text-center">Parcel Name </th>
                            <th class="text-center">No Of Parcel</th>
                            <th class="text-center">Parcel Type</th>
                            {{-- <th class="text-center">TIme </th> --}}
                            <th class="text-center">Visitee</th>
                            <th class="text-center">Property Number</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">{{ $dateTypeName }} Date</th>
                            {{-- <th class="text-center">Purpose</th> --}}
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $sl = 0;
                        @endphp
                        @if ($parcels && !$parcels->isEmpty())
                            @foreach ($parcels as $parcel)
                                @php
                                    $sl++;
                                    $stts_class = '';
                                    $showDate = '';
                                    $stts_text = '';
                                    if ($parcel->handover_status == 'pending') {
                                        $stts_text = 'Pending';
                                        $stts_class = 'exp-status-btn';
                                        $showDate = \Carbon\Carbon::parse($parcel->date . ' ' . $parcel->time)->format(
                                            'd M Y g:i A',
                                        );
                                    } elseif ($parcel->handover_status == 'received') {
                                        $stts_text = 'Received';
                                        $stts_class = 'up-status-btn';
                                        $showDate = \Carbon\Carbon::parse($parcel->received_at)->format('d M Y g:i A');
                                    } elseif ($parcel->handover_status == 'delivered') {
                                        $stts_text = 'Delivered';
                                        $stts_class = 'green-status-btn';
                                        $showDate = \Carbon\Carbon::parse($parcel->handover_at)->format('d M Y g:i A');
                                    }
                                @endphp
                                <tr>
                                    <td class="text-center">{{ $parcel->parcelid }} </td>
                                    <td class="text-center">{{ $parcel->parcel_name }} </td>
                                    <td class="text-center">{{ $parcel->no_of_parcel }}</td>
                                    <td class="text-center">{{ $parcel->parcel_type }}</td>
                                    <td class="text-center">{{ $parcel->member->name ?? '' }}</td>
                                    <td class="text-center">{{ $parcel->member->aprt_no ?? '' }}</td>
                                    <td class="text-center {{ $stts_class }}">
                                        <button class="text-uppercase {{ $stts_class }}">{{ $stts_text }}</button>
                                    </td>
                                    <td class="text-center">
                                        {{ $showDate }}
                                    </td>
                                    {{-- <td class="text-center">{{ \Carbon\Carbon::parse($parcel->date)->format('d M Y') }}
                                    </td>
                                    <td class="text-center">{{ \Carbon\Carbon::parse($parcel->time)->format('g:i A') }}
                                    </td> --}}
                                    {{-- <td class="text-center">{{ $parcel->purpose_of_visit }}</td> --}}
                                    <td class="text-center">
                                        <div class="actions">
                                            <a href="{{ route($thisModule . '.parcel.details', ['id' => $parcel->id]) }}"
                                                id="{{ $parcel->id }}">
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
                                <td colspan="9" class="text-center"> No Data Found </td>
                            </tr>
                        @endif

                    </tbody>
                </table>
                <div class="table_bottom_box">
                    {{-- Pagination Links --}}
                    <div class="d-flex justify-content-between p-2 mt-2 mb-2">
                        <div>
                            Showing {{ $parcels->firstItem() }} to {{ $parcels->lastItem() }} of
                            {{ $parcels->total() }} results
                        </div>
                        <div>
                            {{ $parcels->links('vendor.pagination.bootstrap-5') }}
                            {{-- Bootstrap 5 pagination view --}}
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
            // Function to update the apartment number display
            function updateAprtNoDisplay() {
                // Get the selected option
                const selectedOption = $('#user_id option:selected');
                // Retrieve the 'data-aprtNo' attribute
                const aprtNo = selectedOption.data('aprtno');
                // Check if a valid option is selected
                if (aprtNo) {
                    $('#aprtNoShowData').text('Property Number: ' + aprtNo);
                } else {
                    $('#aprtNoShowData').text(''); // Clear if no valid option is selected
                }
            }

            // Trigger update on page load (in case an option is pre-selected)
            updateAprtNoDisplay();
            $('#user_id').change(function() {
                const selectedOption = $(this).find(':selected');

                // Retrieve data attributes
                const name = selectedOption.data('name') || 'N/A';
                const floor = selectedOption.data('floor') || 'N/A';
                const block = selectedOption.data('block') || 'N/A';
                const unitType = selectedOption.data('unittype') || 'N/A';
                const aprtNo = selectedOption.data('aprtno') || 'N/A';
                const phone = selectedOption.data('phone') || 'N/A';

                // Populate the Resident Details section
                if (selectedOption.val()) {
                    $('#nameShowData').text(name);
                    $('#floorShowData').text(floor);
                    $('#blockShowData').text(block);
                    $('#unitTypeShowData').text(unitType);
                    $('#aprtNoShowData').text('Property Number : ' + aprtNo);
                    $('#phoneShowData').text(phone);

                    // Show the Resident Details card
                    $('#resident-info').fadeIn();
                } else {
                    $('#aprtNoShowData').text('');
                    // Hide the Resident Details card if no valid data is selected
                    $('#resident-info').fadeOut();
                }
            });
        });
    </script>
    <script>
        // Wait for document to be ready
        $(document).ready(function() {
            // Submit handler for the form
            $('#searchForm').submit(function(event) {
                // Clear previous error messages
                $('.err').text('');

                // Get the values of checkin_date and checkout_date
                var checkinDate = $('#from_date').val();
                var checkoutDate = $('#to_date').val();

                // Validation check: If one date is filled and the other is not
                if ((checkinDate && !checkoutDate) || (!checkinDate && checkoutDate)) {
                    // Prevent form submission
                    event.preventDefault();

                    // Show error message in the corresponding input's error span
                    if (!checkinDate) {
                        $('#form_date_error').text('From date is required.');
                    }

                    if (!checkoutDate) {
                        $('#to_date_error').text('To date is required.');
                    }
                }
            });
        });
    </script>
@endpush
