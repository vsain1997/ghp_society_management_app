@extends($thisModule . '::layouts.master')

@section('title', 'Billing')

@section('content')
<div class="right_main_body_content members_page">
    <div class="head_content">
        <div class="left_head">
            {{-- <h2></h2> --}}
            {{-- <p></p> --}}
        </div>
        <!-- Button trigger modal -->
        @can('billing.create')
        <button type="button" class="bg_theme_btn" id="addBilingModalOpen" data-bs-toggle="modal"
            data-bs-target="#addBilingModal">
            <svg width="14" height="15" viewBox="0 0 14 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M13.0005 8.5H8.00049V13.5C8.00049 14.05 7.55049 14.5 7.00049 14.5C6.45049 14.5 6.00049 14.05 6.00049 13.5V8.5H1.00049C0.450488 8.5 0.000488281 8.05 0.000488281 7.5C0.000488281 6.95 0.450488 6.5 1.00049 6.5H6.00049V1.5C6.00049 0.95 6.45049 0.5 7.00049 0.5C7.55049 0.5 8.00049 0.95 8.00049 1.5V6.5H13.0005C13.5505 6.5 14.0005 6.95 14.0005 7.5C14.0005 8.05 13.5505 8.5 13.0005 8.5Z"
                    fill="white" />
            </svg>
            Add Bill
        </button>

        @endcan
    </div>
    <div class="custom_table_wrapper">
        <div class="filter_table_head">
            <div class="search_wrapper search-members-gstr">
                <form action="{{ route($thisModule . '.billing.index') }}" method="GET">
                    {{-- @csrf --}}
                    <div class="input-group">
                        <div class="filter-box">
                            <input type="hidden" name="sid" value="{{ session('__selected_society__') }}">
                            <div class="filter-secl">
                                <select name="user_id" id="user_id2" class="residentsList form-select form-control">
                                    <option value="">--select Resident--</option>
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
                                <span class="text-primary err" id="aprtNoShowData2"></span>
                            </div>
                            <div class="filter-secl">
                                <select name="status" class="form-control">

                                    <option value="none" disabled>--Choose Status--</option>
                                    <option value="unpaid" {{ request('status') == 'unpaid' ? 'selected' : '' }}>
                                        Unpaid
                                    </option>
                                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid
                                    </option>

                                </select>
                            </div>

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
                {{-- <h2>Bills Listing </h2> --}}
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
                        <th class="text-center">Member</th>
                        <th class="text-center">Bill Type</th>
                        <th class="text-center">Service</th>
                        <th class="text-center">Amount</th>
                        <th class="text-center">Due Date</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $sl = 0;
                    @endphp
                    @if ($bills && !$bills->isEmpty())
                    @foreach ($bills as $billing)
                    @php
                    $sl++;
                    $currentDateTime = \Carbon\Carbon::now('Asia/Kolkata');
                    @endphp
                    <tr>
                        <td class="text-center">{{ $billing->user->name }}</td>
                        <td class="text-center">
                            @if ($billing->bill_type == 'my_bill')
                            Utility Bill
                            @else
                            {{ Str::ucfirst(str_replace('_', ' ', $billing->bill_type)) }}
                            @endif
                        </td>
                        <td class="text-center">{{ $billing->service->name }}</td>
                        <td class="text-center">{{ $billing->amount }}</td>
                        <td class="text-center">{{ \Carbon\Carbon::parse($billing->due_date)->format('d M Y') }}
                        </td>
                        <td class="text-center">
                            @php
                            $stts = '';
                            if ($billing->status == 'unpaid') {
                            $stts = 'inactive';
                            } else {
                            $stts = 'active';
                            }
                            @endphp
                            <input type="hidden" name="statusVal" value="{{ parseStatus($stts, 0) }}">
                            @if ($billing->user->role != 'admin' && $billing->status == 'unpaid')
                            <div class="status_select">
                                <select name="status" data-id="{{ $billing->id }}" class="statusOption form-select">
                                    <option value="paid" {{ parseStatus($stts, 1) }}>Paid
                                    </option>
                                    <option value="unpaid" {{ parseStatus($stts, 2) }}>Unpaid</option>
                                </select>
                            </div>
                            @else
                            <span class="status_select">
                                {{ Str::ucfirst(str_replace('_', ' ', $billing->status)) }}
                            </span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="actions">
                                @if ($billing->user->role != 'admin')
                                @can('billing.edit')
                                <a class="edit edit-icon" href="javascript:void(0)" id="{{ $billing->id }}">
                                    <svg width="16" height="17" viewBox="0 0 16 17" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <g clip-path="url(#clip0_193_2081)">
                                            <path
                                                d="M7.33398 3.34825H2.66732C2.3137 3.34825 1.97456 3.48873 1.72451 3.73878C1.47446 3.98882 1.33398 4.32796 1.33398 4.68158V14.0149C1.33398 14.3685 1.47446 14.7077 1.72451 14.9577C1.97456 15.2078 2.3137 15.3483 2.66732 15.3483H12.0007C12.3543 15.3483 12.6934 15.2078 12.9435 14.9577C13.1935 14.7077 13.334 14.3685 13.334 14.0149V9.34825"
                                                stroke="white" stroke-width="1.33333" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                            <path
                                                d="M12.334 2.34825C12.5992 2.08303 12.9589 1.93404 13.334 1.93404C13.7091 1.93404 14.0688 2.08303 14.334 2.34825C14.5992 2.61347 14.7482 2.97318 14.7482 3.34825C14.7482 3.72332 14.5992 4.08303 14.334 4.34825L8.00065 10.6816L5.33398 11.3483L6.00065 8.68158L12.334 2.34825Z"
                                                stroke="white" stroke-width="1.33333" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                        </g>
                                        <defs>
                                            <clipPath id="clip0_193_2081">
                                                <rect width="16" height="16" fill="white"
                                                    transform="translate(0 0.68158)" />
                                            </clipPath>
                                        </defs>
                                    </svg>
                                </a>
                                @endcan
                                @endif
                                <a class="view"
                                    href="{{ route($thisModule . '.billing.details', ['id' => $billing->id]) }}"
                                    id="{{ $billing->id }}">
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
                                @if ($billing->user->role != 'admin')
                                @can('billing.delete')
                                <a class="delete delete-icon" href="javascript:void(0)" data-id="{{ $billing->id }}">
                                    <svg width="16" height="17" viewBox="0 0 16 17" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <g clip-path="url(#clip0_193_2091)">
                                            <path d="M2 4.68158H3.33333H14" stroke="#C90202" stroke-width="1.33333"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                            <path
                                                d="M12.6673 4.68157V14.0149C12.6673 14.3685 12.5268 14.7077 12.2768 14.9577C12.0267 15.2078 11.6876 15.3482 11.334 15.3482H4.66732C4.3137 15.3482 3.97456 15.2078 3.72451 14.9577C3.47446 14.7077 3.33398 14.3685 3.33398 14.0149V4.68157M5.33398 4.68157V3.34824C5.33398 2.99462 5.47446 2.65548 5.72451 2.40543C5.97456 2.15538 6.3137 2.01491 6.66732 2.01491H9.33398C9.68761 2.01491 10.0267 2.15538 10.2768 2.40543C10.5268 2.65548 10.6673 2.99462 10.6673 3.34824V4.68157"
                                                stroke="#C90202" stroke-width="1.33333" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                            <path d="M6.66602 8.01491V12.0149" stroke="#C90202" stroke-width="1.33333"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                            <path d="M9.33398 8.01491V12.0149" stroke="#C90202" stroke-width="1.33333"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                        </g>
                                        <defs>
                                            <clipPath id="clip0_193_2091">
                                                <rect width="16" height="16" fill="white"
                                                    transform="translate(0 0.68158)" />
                                            </clipPath>
                                        </defs>
                                    </svg>
                                </a>
                                @endcan
                                @endif
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
                        Showing {{ $bills->firstItem() }} to {{ $bills->lastItem() }} of
                        {{ $bills->total() }} results
                    </div>
                    <div>
                        {{ $bills->links('vendor.pagination.bootstrap-5') }} {{-- Bootstrap 5 pagination view --}}
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade custom_Modal" id="addBilingModal" tabindex="-1" aria-labelledby="addBilingModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered ">
        <div class="modal-content ">
            <div class="modal-header">
                <h3 class="text-white" id="modalHeadTxt">Add Bill</h3>
                {{-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> --}}
            </div>
            <div class="modal-body">
                <div class="custom_form">
                    <form method="POST" action="{{ route($thisModule . '.billing.store') }}" id="addBilingForm">
                        @csrf
                        <div class="row d-none">
                            <div class="col">
                                <div class="form-group">
                                    <input type="hidden" name="id" id="id">
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
                        </div>
                        <div class="row mt-3" id="resident-info" style="display: none;">
                            <div class="col">
                                <div class="card">
                                    <div class="card-header text-white" style="background-color: #8077F5;">Resident
                                        Details</div>
                                    <div class="card-body">
                                        <p><strong>Name:</strong> <span id="nameShowData"></span></p>
                                        <p><strong>Tower:</strong> <span id="blockShowData"></span></p>
                                        <p><strong>Floor:</strong> <span id="floorShowData"></span></p>
                                        <p><strong>Property Type:</strong> <span id="unitTypeShowData"></span></p>
                                        <p><strong>Property Number:</strong> <span id="aprtNoShowData"></span></p>
                                        <p><strong>Phone:</strong> <span id="phoneShowData"></span></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col overflow-hidden">
                                <div class="form-group">
                                    <label for="user_id">Select Resident</label>
                                    <select name="user_id" id="user_id" class="residentList form-select form-control">
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
                            </div>
                            <div class="col overflow-hidden">
                                <div class="form-group">
                                    <label for="bill_type">Bill Type</label>
                                    <select name="bill_type" id="bill_type" class="form-select form-control">
                                        <option value="">--select--</option>
                                        <option value="my_bill">Utility Bill</option>
                                        <option value="maintenance">Maintenance</option>
                                    </select>
                                    <span class="text-danger err"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col overflow-hidden">
                                <div class="form-group">
                                    <label for="service_id">Select Service</label>
                                    <select name="service_id" id="service_id"
                                        class="residentList form-select form-control">
                                        <option value="">--select--</option>
                                        @if (!empty($billServices))
                                        @foreach ($billServices as $services)
                                        <option value="{{ $services->id }}">
                                            {{ $services->name }}</option>
                                        @endforeach
                                        @endif
                                    </select>
                                    <span class="text-danger err"></span>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label for="amount">Amount</label>
                                    <input type="text" name="amount" id="amount" class="form-control">
                                    <span class="text-danger err"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="form-group">
                                    <label for="due_date">Due Date</label>
                                    <input type="date" name="due_date" id="due_date" class="form-control">
                                    <span class="text-danger err"></span>
                                </div>
                            </div>
                        </div>
                        <div class="save-close-btn">
                            <button type="button" class="close-btn cancel_btn" data-bs-dismiss="modal">Close</button>
                            <button type="button" data-formtype="add" id="submitAddBilingForm"
                                class="submint-btn">Submit</button>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('footer-script')
<script>


    $(".members_page").on('click', '#addBilingModalOpen', function (event) {
        // set modal form
        $('#addBilingForm').attr('action',
            '{{ route($thisModule . '.billing.store') }}'
        );
        $('#submitAddBilingForm').attr('data-formtype', 'add');
        $('#modalHeadTxt').text('Add Bill');
        $('#submitAddBilingForm').text('Submit');

        $('#resident-info').fadeOut();

        // reset form data
        $('#addBilingForm').find(
            'input:not([name="_token"],[name^="society_id"],[name^="created_by"]), select, textarea').each(
                function () {
                    if ($(this).is('select')) {
                        // Reset Select2 dropdowns
                        $(this).val("").trigger('change'); // Reset the value
                        const firstOption = $(this).find('option:first');
                        // console.log("-----------------------open--------------------");
                        // console.log(firstOption);

                        // Update the text of the first option
                        if (firstOption.length) {
                            firstOption.text('--select--'); // Change the text of the first option
                        }

                        // Refresh the Select2 dropdown to reflect changes
                        $(this).select2({
                            width: '100%',
                            dropdownParent: $('.modal-content'),
                            minimumResultsForSearch: 0 // Always show the search box
                        });

                    } else {
                        // console.log("-----------------------open input--------------------");
                        // Reset other input and textarea fields
                        $(this).val('');
                    }
                });
        $('#society_id').val({{ session('__selected_society__') }});
    $('.err').text('');
        });
</script>
{{-- add + edit form validation and submit --}}
<script>
    $(document).ready(function () {
        // disable modal outside click
        $('#addBilingModal').modal({
            backdrop: 'static',
            keyboard: false
        });
        // check form1 validation and switch to next form2
        $(".modal").on('click', '#submitAddBilingForm', async function (event) {
            event.preventDefault();
            // loader add
            $('#loader').css('width', '50%');
            $('#loader').fadeIn();
            $('#blockOverlay').fadeIn();

            // let formType = $('#submitAddBilingForm').data('formtype');
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

                $('#addBilingForm').submit();
            } else {
                //on modal-cancel relaod page to show fresh updated data
                $('#addBilingForm').find('.cancel_btn').attr('onclick',
                    'window.location.reload()');
                let formData = $('#addBilingForm').serialize();
                $.ajax({
                    url: $('#addBilingForm').attr('action'),
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

    function isPastDate(dueDate) {
        const selectedDate = new Date(dueDate); // Convert input to Date object
        const today = new Date();

        // Reset time for accurate comparison
        today.setHours(0, 0, 0, 0);

        return selectedDate < today;
    }

    //validate
    async function validateForm(formType) {
        let hasError = 0;
        // console.log('called', formType);

        // Assign form values to variables
        let societyId = $('#society_id').val();
        let user_id = $('#user_id').val();
        let bill_type = $('#bill_type').val();
        let service_id = $('#service_id').val();
        let amount = $('#amount').val().trim();
        let due_date = $('#due_date').val().trim();
        $('.err').text('');

        if (amount === '') {
            $('#amount').siblings('.err').text('Amount is required !');
            hasError = 1;
        } else if (amount < 1) {
            $('#amount').siblings('.err').text('Invalid amount !');
            hasError = 1;
        }

        if (user_id === '') {
            $('#user_id').siblings('.err').text('Resident is required !');
            hasError = 1;
        }
        if (bill_type === '') {
            $('#bill_type').siblings('.err').text('Bill Type is required !');
            hasError = 1;
        }
        if (service_id === '') {
            $('#service_id').siblings('.err').text('Service is required !');
            hasError = 1;
        }

        if (due_date === '') {
            $('#due_date').siblings('.err').text('Due date is required !');
            hasError = 1;
        } else if (isPastDate(due_date)) {
            $('#due_date').siblings('.err').text('Past due date is not allowed !');
            hasError = 1;
        }

        // Validate society_id
        if (societyId === '' || isNaN(societyId)) {
            $('#society_id').siblings('.err').text('Society is required and must be a valid number !');
            hasError = 1;
        }

        // Return 1 if error is found, otherwise return 0
        return hasError;
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

            // $('#addBilingForm')[0].reset();
            $('#addBilingForm').find(
                'input:not([name="_token"],[name^="unit_type"],[name^="created_by"]), select, textarea'
            ).each(
                function () {
                    if ($(this).is('select')) {
                        // Reset Select2 dropdowns
                        $(this).val("").trigger('change'); // Reset the value
                        const firstOption = $(this).find('option:first');
                        // console.log("-----------------------open--------------------");
                        // console.log(firstOption);

                        // Update the text of the first option
                        if (firstOption.length) {
                            firstOption.text('--select--'); // Change the text of the first option
                        }

                        // Refresh the Select2 dropdown to reflect changes
                        $(this).select2({
                            width: '100%',
                            dropdownParent: $('.modal-content'),
                            minimumResultsForSearch: 0 // Always show the search box
                        });

                    } else {
                        // console.log("-----------------------open input--------------------");
                        // Reset other input and textarea fields
                        $(this).val('');
                    }
                });

            $('#addBilingForm select').each(function () {
                $(this).prop('selectedIndex', 0); // Select the first option
            });
            // disable outside click + exc press
            $('#addBilingModal').modal({
                backdrop: 'static',
                keyboard: false
            })
            // change modal heading
            $('#modalHeadTxt').text('Edit Bill');
            $('#submitAddBilingForm').text('Update');

            $('#resident-info').fadeOut();

            const billingId = $(this).attr('id');

            $.ajax({
                url: "{{ route($thisModule . '.billing.edit', ['id' => ':billingId']) }}"
                    .replace(':billingId', billingId),
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
                    $('#addBilingForm').attr('action',
                        '{{ route($thisModule . '.billing.update', ['id' => '__ID__']) }}'
                            .replace('__ID__', data.id));
                    $('#submitAddBilingForm').attr('data-formtype', 'edit');

                    //feed #addBilingForm form data by jq below
                    $('#id').val(data.id);
                    $('#user_id').val(data.user_id).trigger('change');
                    $('#bill_type').val(data.bill_type).trigger('change');
                    $('#service_id').val(data.service_id).trigger('change');
                    $('#amount').val(data.amount);
                    $('#due_date').val(data.due_date);
                    $('#society_id').val(data.society_id);

                    //loader removed
                    $('#loader').css('width', '100%');
                    $('#loader').fadeOut();
                    $('#blockOverlay').fadeOut();

                    $('#addBilingModal').modal('show');
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
            var billingId = $(this).data('id');
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
                        url: "{{ route($thisModule . '.billing.delete', ['id' => ':billingId']) }}"
                            .replace(':billingId', billingId),
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
<script>
    $(document).ready(function () {
        $('.residentList').select2({

            width: '100%',
            dropdownParent: $('.modal-content'),
            minimumResultsForSearch: 0 // Always show the search box
        });
        $('#user_id').change(function () {
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
                $('#aprtNoShowData').text(aprtNo);
                $('#phoneShowData').text(phone);

                // Show the Resident Details card
                $('#resident-info').fadeIn();
            } else {
                // Hide the Resident Details card if no valid data is selected
                $('#resident-info').fadeOut();
            }
        });

        $('#user_id2').change(function () {
            const selectedOption = $(this).find(':selected');

            // Retrieve data attributes
            const aprtNo = selectedOption.data('aprtno') || 'N/A';
            // Populate the Resident Details section
            if (selectedOption.val()) {
                $('#aprtNoShowData2').text('Property Number : '+aprtNo);
                // Show the Resident Details card
            } else {
                // Hide the Resident Details card if no valid data is selected
                $('#aprtNoShowData2').text('');
            }
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
        if (nowStts == 'paid') {
            preSttsS = 'unpaid';
        } else {

            preSttsS = 'paid';
        }


        $hiddenInput.val((+$hiddenInput.val() === 0) ? 1 : 0);
        console.log($hiddenInput.val());

        if ($hiddenInput.val() == 1) {
            var toStatus = 'paid';
        } else {

            var toStatus = 'unpaid';
        }
        console.log(toStatus);


        var memberId = $(this).data('id');
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
                    url: "{{ route($thisModule . '.billing.status.change', ['id' => ':memberId', 'status' => ':toStatus']) }}"
                        .replace(':memberId', memberId).replace(':toStatus', toStatus),
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
@endpush
