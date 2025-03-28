@extends($thisModule . '::layouts.master')

@section('title', 'Service Providers')

@section('content')
<div class="right_main_body_content members_page">
    <div class="head_content">
        <div class="left_head">
            {{-- <h2></h2> --}}
            {{-- <p>Add or manage serviceProviders of the society</p> --}}
        </div>
        <!-- Button trigger modal -->
        @can('service_provider.create')
        <button type="button" class="bg_theme_btn" id="addServiceProviderModalOpen" data-bs-toggle="modal"
            data-bs-target="#addServiceProviderModal">
            <svg width="14" height="15" viewBox="0 0 14 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M13.0005 8.5H8.00049V13.5C8.00049 14.05 7.55049 14.5 7.00049 14.5C6.45049 14.5 6.00049 14.05 6.00049 13.5V8.5H1.00049C0.450488 8.5 0.000488281 8.05 0.000488281 7.5C0.000488281 6.95 0.450488 6.5 1.00049 6.5H6.00049V1.5C6.00049 0.95 6.45049 0.5 7.00049 0.5C7.55049 0.5 8.00049 0.95 8.00049 1.5V6.5H13.0005C13.5505 6.5 14.0005 6.95 14.0005 7.5C14.0005 8.05 13.5505 8.5 13.0005 8.5Z"
                    fill="white" />
            </svg>
            Add Service Provider
        </button>
        @endcan
    </div>
    <div class="custom_table_wrapper">
        <div class="filter_table_head">
            <div class="search_wrapper search-members-gstr">
                <form action="{{ route($thisModule . '.service_provider.index') }}" method="GET">
                    {{-- @csrf --}}
                    <div class="input-group">
                        <input type="hidden" name="sid" value="{{ session('__selected_society__') }}">
                        <div class="filter-box">
                            <div class="filter-secl">
                                <select name="status" class="form-control">
                                    <option value="none" disabled>--Choose Status--</option>
                                    <option value="active" {{ request('status')=='active' ? 'selected' : '' }}>Active
                                    </option>
                                    <option value="inactive" {{ request('status')=='inactive' ? 'selected' : '' }}>
                                        Inactive
                                    </option>
                                </select>
                            </div>

                            <div class="filter-secl">
                                <select name="search_for" class="form-control">
                                    <option value="none" disabled>--Search By--</option>
                                    <option value="name" {{ request('search_for')=='name' ? 'selected' : '' }}>Name
                                    </option>
                                    <option value="phone" {{ request('search_for')=='phone' ? 'selected' : '' }}>
                                        Contact
                                    </option>
                                    <option value="email" {{ request('search_for')=='email' ? 'selected' : '' }}>
                                        Email
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
                {{-- <h2> Service Providers ( <span class="text-muted">External</span> ) Listing </h2> --}}
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
                        <th class="text-center">Category</th>
                        <th class="text-center">Phone</th>
                        <th class="text-center">Email</th>
                        <th class="text-center">Address</th>
                        <th class="text-center">status</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $sl = 0;
                    @endphp
                    @if ($serviceProviders && !$serviceProviders->isEmpty())
                    @foreach ($serviceProviders as $sprovider)
                    @php
                    $sl++;
                    @endphp
                    <tr>
                        <td class="text-center">{{ $sprovider->name }}</td>
                        <td class="text-center">{{ $sprovider->serviceCategory->name }}</td>
                        <td class="text-center">{{ $sprovider->phone }}</td>
                        <td class="text-center">{{ $sprovider->email }}</td>

                        <td class="text-center">
                            @if (strlen($sprovider->address) > 50)
                            {{ substr($sprovider->address, 0, 47) . '...' }}
                            @else
                            {{ $sprovider->address }}
                            @endif
                        </td>
                        <td class="text-center">
                            <input type="hidden" name="statusVal" value="{{ parseStatus($sprovider->status, 0) }}">
                            @can('service_provider.change_status')
                            <div class="status_select">
                                <select name="status" data-id="{{ $sprovider->id }}" class="statusOption form-select">
                                    <option value="active" {{ parseStatus($sprovider->status, 1) }}>Active
                                    </option>
                                    <option value="inactive" {{ parseStatus($sprovider->status, 2) }}>Inactive</option>
                                </select>
                            </div>
                            @endcan
                            @cannot('service_provider.change_status')
                            <span class="status_select">
                                {{ Str::ucfirst(str_replace('_', '', $sprovider->status)) }}
                            </span>
                            @endcannot
                        </td>
                        <td class="text-center">
                            <div class="actions">
                                @can('service_provider.edit')
                                <a class="edit edit-icon" href="javascript:void(0)" id="{{ $sprovider->id }}">
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
                                @can('service_provider.delete')
                                <a class="delete delete-icon" href="javascript:void(0)" data-id="{{ $sprovider->id }}">
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
                        Showing {{ $serviceProviders->firstItem() }} to {{ $serviceProviders->lastItem() }} of
                        {{ $serviceProviders->total() }} results
                    </div>
                    <div>
                        {{ $serviceProviders->links('vendor.pagination.bootstrap-5') }} {{-- Bootstrap 5 pagination view
                        --}}
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade custom_Modal" id="addServiceProviderModal" tabindex="-1"
    aria-labelledby="addServiceProviderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content ">
            <div class="modal-header">
                <h3 class="text-white" id="modalHeadTxt">Add Service Provider</h3>
                {{-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> --}}
            </div>
            <div class="modal-body">
                <div class="custom_form">
                    <form method="POST" action="{{ route($thisModule . '.service_provider.store') }}"
                        id="addServiceProviderForm">
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
                                    <label for="name">Name</label>
                                    <input type="text" name="name" id="name" class="form-control">
                                    <span class="text-danger err"></span>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-grooup">
                                    <label for="category">Category</label>
                                    <select name="category" id="category" class="form-select form-control ">
                                        <option value="" selected>--select--</option>
                                        @foreach ($serviceCategories as $scategory)
                                        <option value="{{ $scategory->id }}">{{ $scategory->name }}</option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger err"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="form-group">
                                    <label for="date">Phone</label>
                                    <input type="text" name="phone" id="phone"
                                        class="form-control phone-input-restrict">
                                    <span class="text-danger err"></span>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label for="time">Email</label>
                                    <input type="email" name="email" id="email" class="form-control">
                                    <span class="text-danger err"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-grooup">
                                <label for="address">Address</label>
                                <input type="text" name="address" id="address" class="form-control">
                                <span class="text-danger err"></span>
                            </div>
                        </div>
                        <div class="save-close-btn">
                            <button type="button" class="close-btn cancel_btn" data-bs-dismiss="modal">Close</button>
                            <button type="button" data-formtype="add" id="submitaddServiceProviderForm"
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
    $(".members_page").on('click', '#addServiceProviderModalOpen', function (event) {
        // set modal form
        $('#addServiceProviderForm').attr('action',
            '{{ route($thisModule . '.service_provider.store') }}'
        );
        $('#submitaddServiceProviderForm').attr('data-formtype', 'add');
        $('#modalHeadTxt').text('Add Service Provider');
        $('#submitaddServiceProviderForm').text('Submit');

        // reset form data
        $('#addServiceProviderForm').find(
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
        // disable modal outside click
        $('#addServiceProviderModal').modal({
            backdrop: 'static',
            keyboard: false
        });

        // check form1 validation and switch to next form2
        $(".modal").on('click', '#submitaddServiceProviderForm', async function (event) {
            event.preventDefault();
            // loader add
            $('#loader').css('width', '50%');
            $('#loader').fadeIn();
            $('#blockOverlay').fadeIn();

            // let formType = $('#submitaddServiceProviderForm').data('formtype');
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

                $('#addServiceProviderForm').submit();
            } else {
                //on modal-cancel relaod page to show fresh updated data
                $('#addServiceProviderForm').find('.cancel_btn').attr('onclick',
                    'window.location.reload()');
                let formData = $('#addServiceProviderForm').serialize();
                $.ajax({
                    url: $('#addServiceProviderForm').attr('action'),
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
        let category = $('#category').val().trim();
        let phone = $('#phone').val().trim();
        let email = $('#email').val().trim();
        let address = $('#address').val().trim();
        let userId = $('#user_id').val().trim();
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

        if (category === '') {
            $('#category').siblings('.err').text('Please Select Category');
            hasError = 1;
        }

        // Validate phone
        if (phone === '') {
            $('#phone').next('.err').text('Contact is required');
            hasError = 1;
        } else if (!validatePhone(phone)) {
            $('#phone').next('.err').text('Invalid phone number');
            hasError = 1;
        } else if (resultPhone) {
            $('#phone').next('.err').text('Already exists !');
            hasError = 1;
        }

        // Validate email
        if (email === '') {
            $('#email').next('.err').text('Email is required');
            hasError = 1;
        } else if (!validateEmail(email)) {
            $('#email').next('.err').text('Invalid email address');
            hasError = 1;
        } else if (resultEmail) {
            $('#email').next('.err').text('Already exists !');
            hasError = 1;
        }

        if (address === '') {
            $('#address').siblings('.err').text('Address is required');
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

            // $('#addServiceProviderForm')[0].reset();
            $('#addServiceProviderForm').find(
                'input:not([name="_token"],[name^="created_by"]), select, textarea'
            ).each(
                function () {
                    $(this).val('');
                });

            $('#addServiceProviderForm select').each(function () {
                $(this).prop('selectedIndex', 0); // Select the first option
            });
            // disable outside click + exc press
            $('#addServiceProviderModal').modal({
                backdrop: 'static',
                keyboard: false
            })
            // change modal heading
            $('#modalHeadTxt').text('Edit Service Provider');
            $('#submitaddServiceProviderForm').text('Update');

            const serviceProviderId = $(this).attr('id');

            $.ajax({
                url: "{{ route($thisModule . '.service_provider.edit', ['id' => ':serviceProviderId']) }}"
                    .replace(':serviceProviderId', serviceProviderId),
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
                    $('#addServiceProviderForm').attr('action',
                        '{{ route($thisModule . '.service_provider.update', ['id' => '__ID__']) }}'
                            .replace('__ID__', data.id));
                    $('#submitaddServiceProviderForm').attr('data-formtype', 'edit');

                    //feed #addServiceProviderForm form data by jq below
                    $('#id').val(data.id);
                    $('#user_id').val(data.user_id);
                    $('#society_id').val(data.society_id);
                    $('#name').val(data.name);

                    if (data.service_category_id != null && data.service_category_id !==
                        '') {
                        if (data.service_category_id.toString().length > 0) {
                            $('#category').val(data.service_category_id);
                        }
                    }

                    $('#phone').val(data.phone);
                    $('#email').val(data.email);
                    $('#address').val(data.address);

                    //loader removed
                    $('#loader').css('width', '100%');
                    $('#loader').fadeOut();
                    $('#blockOverlay').fadeOut();

                    $('#addServiceProviderModal').modal('show');
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
            var serviceProviderId = $(this).data('id');
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
                        url: "{{ route($thisModule . '.service_provider.delete', ['id' => ':serviceProviderId']) }}"
                            .replace(':serviceProviderId', serviceProviderId),
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
        let nowStts = $('.statusOption').val();
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


        var serviceProviderId = $(this).data('id');
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
                    url: "{{ route($thisModule . '.service_provider.status.change', ['id' => ':serviceProviderId', 'status' => ':toStatus']) }}"
                        .replace(':serviceProviderId', serviceProviderId).replace(':toStatus',
                            toStatus),
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
                $('.statusOption').val(preSttsS); // Reset the status option
                $hiddenInput.val(preSttsN); // Reset the hidden input value
            }
        });
    });
</script>
@endpush