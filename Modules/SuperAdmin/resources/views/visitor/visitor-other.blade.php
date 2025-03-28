@extends($thisModule . '::layouts.master')

@section('title', 'Other Visitors')

@section('content')
    <div class="right_main_body_content members_page ">

        <div class="head_content">
            <div class="left_head">
                <h2>Other Visitors</h2>
            </div>
        </div>

        <div class="custom_table_wrapper">
            <div class="filter_table_head visitor-sec">

                <div class="search_wrapper search-members-gstr">
                    <form method="GET" id="searchForm">

                        <div class="input-group">
                            <div class="filter-box">
                                <div class="filter-secl">
                                    <label for="checkin_date">Checkin Date</label>
                                    <input type="date" class="form-control" id="checkin_date" name="checkin_date"
                                        value="{{ request('checkin_date') }}">
                                    <span class="text-danger" id="checkin_date_error"></span>
                                </div>
                                <div class="filter-secl">
                                    <label for="checkout_date">Checkout Date</label>
                                    <input type="date" class="form-control" id="checkout_date" name="checkout_date"
                                        value="{{ request('checkout_date') }}">
                                    <span class="text-danger" id="checkout_date_error"></span>
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
                    {{-- <h2>Visitors </h2> --}}
                </div>
            </div>
            <div class="table-responsive visitors-table">
                <table width="100%" cellpadding="0" cellspacing="0">
                    <thead>
                        <tr>
                            {{-- <th class="text-center">Type of Visitor</th> --}}
                            <th class="text-center">Visitor Name </th>
                            <th class="text-center">Visitor Number </th>
                            <th class="text-center">Date</th>
                            <th class="text-center">no of visitors</th>
                            {{-- <th class="text-center">TIme </th> --}}
                            <th class="text-center">Recent CheckIn</th>
                            <th class="text-center">Recent CheckOut</th>
                            {{-- <th class="text-center">Purpose</th> --}}
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $sl = 0;
                        @endphp
                        @if ($visitors && !$visitors->isEmpty())
                            @foreach ($visitors as $visitor)
                                @php
                                    $sl++;
                                @endphp
                                <tr>
                                    {{-- <td class="text-center">{{ $visitor->type_of_visitor }} </td> --}}
                                    <td class="text-center">{{ $visitor->visitor_name }} </td>
                                    <td class="text-center">{{ $visitor->phone }}</td>

                                    <td class="text-center">
                                        @if (!empty($visitor->date) && !empty($visitor->time))
                                            {{ \Carbon\Carbon::parse($visitor->date.' '.$visitor->time)->format('d M Y g:i A') }}
                                        @endif
                                    </td>

                                    <td class="text-center">{{ $visitor->no_of_visitors }}</td>
                                    <td class="text-center">
                                        @if (!empty($visitor->lastCheckinDetail->checkin_at))
                                            {{ \Carbon\Carbon::parse($visitor->lastCheckinDetail->checkin_at)->format('d M Y g:i A') }}
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if (!empty($visitor->lastCheckinDetail->checkout_at))
                                            {{ \Carbon\Carbon::parse($visitor->lastCheckinDetail->checkout_at)->format('d M Y g:i A') }}
                                        @endif
                                    </td>
                                    {{-- <td class="text-center">{{ \Carbon\Carbon::parse($visitor->date)->format('d M Y') }}
                                    </td>
                                    <td class="text-center">{{ \Carbon\Carbon::parse($visitor->time)->format('g:i A') }}
                                    </td> --}}
                                    {{-- <td class="text-center">{{ $visitor->purpose_of_visit }}</td> --}}
                                    <td class="text-center">
                                        <div class="actions">
                                            <a href="{{ route($thisModule . '.visitor-other.details', ['id' => $visitor->id]) }}"
                                                id="{{ $visitor->id }}">
                                                <img src="{{ url($thisModule) }}/img/eye.png" alt="eye">
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
                            Showing {{ $visitors->firstItem() }} to {{ $visitors->lastItem() }} of
                            {{ $visitors->total() }} results
                        </div>
                        <div>
                            {{ $visitors->links('vendor.pagination.bootstrap-5') }}
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
                    // $('#nameShowData').text(name);
                    // $('#floorShowData').text(floor);
                    // $('#blockShowData').text(block);
                    // $('#unitTypeShowData').text(unitType);
                    $('#aprtNoShowData').text('Property Number: ' + aprtNo);
                    // $('#phoneShowData').text(phone);

                    // Show the Resident Details card
                    // $('#resident-info').fadeIn();
                } else {
                    $('#aprtNoShowData').text('');
                    // Hide the Resident Details card if no valid data is selected
                    // $('#resident-info').fadeOut();
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
                var checkinDate = $('#checkin_date').val();
                var checkoutDate = $('#checkout_date').val();

                // Validation check: If one date is filled and the other is not
                if ((checkinDate && !checkoutDate) || (!checkinDate && checkoutDate)) {
                    // Prevent form submission
                    event.preventDefault();

                    // Show error message in the corresponding input's error span
                    if (!checkinDate) {
                        $('#checkin_date_error').text('Check-in date is required.');
                    }

                    if (!checkoutDate) {
                        $('#checkout_date_error').text('Checkout date is required.');
                    }
                }
            });
        });
    </script>
@endpush
