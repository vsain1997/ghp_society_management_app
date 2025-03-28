@extends($thisModule . '::layouts.master')

@section('title', 'Members')

@section('content')
    <div class="right_main_body_content members_page">
        <div class="head_content">
            <div class="left_head">
                <h2>Members</h2>
                <p>Add or manage members of the society</p>
            </div>
            <!-- Button trigger modal -->
            <button type="button" id="openModal" class="bg_theme_btn" data-bs-toggle="modal" data-bs-target="#addMemberModal">
                <svg width="14" height="15" viewBox="0 0 14 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M13.0005 8.5H8.00049V13.5C8.00049 14.05 7.55049 14.5 7.00049 14.5C6.45049 14.5 6.00049 14.05 6.00049 13.5V8.5H1.00049C0.450488 8.5 0.000488281 8.05 0.000488281 7.5C0.000488281 6.95 0.450488 6.5 1.00049 6.5H6.00049V1.5C6.00049 0.95 6.45049 0.5 7.00049 0.5C7.55049 0.5 8.00049 0.95 8.00049 1.5V6.5H13.0005C13.5505 6.5 14.0005 6.95 14.0005 7.5C14.0005 8.05 13.5505 8.5 13.0005 8.5Z"
                        fill="white" />
                </svg>
                Add New Member
            </button>
        </div>
        <div class="custom_table_wrapper">
            <div class="filter_table_head">
                <div class="search_wrapper">
                    <form action="{{ route($thisModule . '.member.index') }}" method="GET">
                        @csrf
                        <div class="input-group">
                            {{--
                            <button class="searchIcon">
                                <svg width="24" height="25" viewBox="0 0 24 25" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M11.5 21.5C16.7467 21.5 21 17.2467 21 12C21 6.75329 16.7467 2.5 11.5 2.5C6.25329 2.5 2 6.75329 2 12C2 17.2467 6.25329 21.5 11.5 21.5Z"
                                        stroke="#292D32" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M22 22.5L20 20.5" stroke="#292D32" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                </svg>
                            </button>
                            --}}
                            <input type="search" name="search" id="search" placeholder="Search"
                                value="{{ request('search') }}">
                            <button type="submit" class="bg_theme_btn">
                                <svg width="24" height="25" viewBox="0 0 24 25" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M11.5 21.5C16.7467 21.5 21 17.2467 21 12C21 6.75329 16.7467 2.5 11.5 2.5C6.25329 2.5 2 6.75329 2 12C2 17.2467 6.25329 21.5 11.5 21.5Z"
                                        stroke="#292D32" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                    <path d="M22 22.5L20 20.5" stroke="#292D32" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                </svg>
                                Search
                            </button>
                        </div>
                    </form>
                </div>
                <div class="right_filters">
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
                            <th>Name</th>
                            <th>Role</th>
                            <th>Block</th>
                            <th>Floor</th>
                            <th>Aprt No.</th>
                            <th>Contact</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $sl = 0;
                        @endphp
                        @if ($members && !$members->isEmpty())
                            @foreach ($members as $member)
                                @php
                                    $sl++;
                                @endphp
                                <tr>
                                    <td>{{ $member->name }}</td>
                                    <td>{{ $member->role }}</td>
                                    <td>{{ $member->block->name ?? '' }}</td>
                                    <td>{{ $member->floor_number }}</td>
                                    <td>{{ $member->aprt_no }}</td>
                                    <td>{{ $member->phone }}</td>
                                    <td>{{ $member->email }}</td>
                                    <td>
                                        <input type="hidden" name="statusVal"
                                            value="{{ parseStatus($member->status, 0) }}">
                                        <div class="status_select">
                                            <select name="status" class="statusOption form-select">
                                                <option value="active" {{ parseStatus($member->status, 1) }}>Active
                                                </option>
                                                <option value="inactive" {{ parseStatus($member->status, 2) }}>Inactive</option>
                                            </select>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="actions">
                                            <a href="javascript:void(0)" id="{{ $member->id }}" class="edit">
                                                <img src="{{ url($thisModule) }}/img/edit.png" alt="edit">
                                            </a>
                                            <a href="javascript:void(0)" id="{{ $member->id }}" class="view">
                                                <img src="{{ url($thisModule) }}/img/eye.png" alt="eye">
                                            </a>
                                            <a href="javascript:void(0)" data-id="{{ $member->id }}" class="delete">
                                                <img src="{{ url($thisModule) }}/img/delete.png" alt="delete">
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
                        @if ($members && !$members->isEmpty())
                            <div>
                                Showing {{ $members->firstItem() }} to {{ $members->lastItem() }} of
                                {{ $members->total() }} results

                            </div>
                            <div>
                                {{ $members->links('vendor.pagination.bootstrap-5') }}
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade custom_Modal" id="addMemberModal" tabindex="-1" aria-labelledby="addMemberModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content ">
                <div class="modal-header">
                    <h3 id="modalHeadTxt">Add New Member</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="custom_form">
                        <form method="POST" action="{{ route($thisModule . '.member.store') }}" id="addMemberForm">
                            @csrf
                            <input type="hidden" name="id" id="id">
                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <label for="name">Name</label>
                                        <input type="text" name="name" id="name" class="form-control">
                                        <span class="text-danger err"></span>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <label for="role">Role</label>
                                        <select name="role" id="role" class="form-select form-control ">
                                            <option value="" selected>--select--</option>
                                            <option value="resident">Resident</option>
                                            <option value="admin">Admin</option>
                                        </select>
                                        <span class="text-danger err"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <label for="phone">Contact</label>
                                        <input type="text" name="phone" id="phone" value=""
                                            onkeyup="checkDuplicate('phone',this)" class="form-control">
                                        <span class="text-danger err"></span>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input type="text" name="email" id="email" value=""
                                            onkeyup="checkDuplicate('email',this)" class="form-control">
                                        <span class="text-danger err"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <label for="society_id">Society</label>
                                        <select name="society_id" id="society_id" class="form-select form-control"
                                            onchange="showBlocks(this)">
                                            <option value="" selected>--select--</option>
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
                                        </select>
                                        <span class="text-danger err"></span>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <label for="block_id">Block</label>
                                        <select name="block_id" id="block_id" class="form-select form-control ">
                                            <option value="" selected>--select--</option>
                                        </select>
                                        <span class="text-danger err"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <label for="floor_number">Floor</label>
                                        <select name="floor_number" id="floor_number" class="form-select form-control ">
                                            <option value="" selected>--select--</option>
                                        </select>
                                        <span class="text-danger err"></span>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <label for="unit_type">Unit Type</label>
                                        <select name="unit_type" id="unit_type" class="form-select form-control ">
                                            <option value="" selected>--select--</option>
                                        </select>
                                        <span class="text-danger err"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <label for="aprt_no">Aprt No.</label>
                                        <input type="text" name="aprt_no" id="aprt_no" class="form-control">
                                        <span class="text-danger err"></span>
                                    </div>
                                </div>

                            </div>
                            <div class="row">
                                <div class="col text-end">
                                    <button type="button" class="border_theme_btn cancel_btn"
                                        data-bs-dismiss="modal">Cancel</button>
                                    <button type="button" data-formtype="add" id="submitAddMemberForm"
                                        class="bg_theme_btn">Submit</button>
                                </div>
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
        $(".members_page").on('click', '#openModal', function(event) {
            showBlocks({{ session('__selected_society__') }}, function() {
                console.log();
            });
        });

        function showBlocks(s, callback) {
            let societyId = 0;
            if (callback && typeof callback === 'function') {

                societyId = s;
            } else {

                societyId = Number(s.value);
            }
            let url = '{{ route($thisModule . '.get.blocks') }}';
            let method = 'POST';
            let headers = {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            };
            let body = {
                society_id: societyId
            };

            fetchData(url, method, headers, body)
                .then(data => {
                    if (data) {
                        $('#block_id').empty();
                        $('#unit_type').empty();
                        $('#floor_number').empty();
                        $.each(data.blocks, function(index, block) {
                            $('#block_id').append('<option value="' + block.id + '">' + block.name +
                                '</option>');
                            $('#unit_type').append('<option value="' + block.unit_type + '">' + block
                                .unit_type + '</option>');
                        });
                        let floors = data.blocks[0].society.floors;
                        for (let i = 1; i < floors + 1; i++) {
                            $('#floor_number').append('<option value="' + i + '">' + i + '</option>');
                        }

                        if (callback && typeof callback === 'function') {
                            callback();
                        }

                    }
                });
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
    </script>
    {{-- add + edit form validation and submit --}}
    <script>
        $(document).ready(function() {
            // check form1 validation and switch to next form2
            $(".modal").on('click', '#submitAddMemberForm', async function(event) {
                event.preventDefault();
                $('.err').text('');
                // let formType = $('#submitAddMemberForm').data('formtype');
                let formType = $('#id').val();
                if (formType > 0 && formType) {
                    formType = 'edit';
                } else {
                    formType = 'add';
                }
                console.log('call', formType);

                let validationStatus = await validateForm(formType);
                if (validationStatus != 0) {
                    // toastr.error('Kindly complete all fields accurately !')
                    return false;
                }

                //direct submit for add and ajax submit for edit
                if (formType == 'add') {
                    $('#addMemberForm').submit();
                } else {
                    //on modal-cancel relaod page to show fresh updated data
                    $('#addMemberForm').find('.cancel_btn').attr('onclick',
                        'window.location.reload()');
                    let formData = $('#addMemberForm').serialize();
                    $.ajax({
                        url: $('#addMemberForm').attr('action'),
                        method: 'POST',
                        data: formData,
                        success: function(response) {
                            toastr[response.status](response.message);
                        },
                        error: function(xhr, status, error) {
                            toastr[response.status](response.message);
                        }
                    });
                }
            });
        });

        //validate
        async function validateForm(formType) {
            let hasError = 0;
            console.log('called', formType);

            // Assign form values to variables
            let userId = $('#id').val().trim();
            let name = $('#name').val().trim();
            let role = $('#role').val();
            let phone = $('#phone').val().trim();
            let email = $('#email').val().trim();
            let societyId = $('#society_id').val();
            let blockId = $('#block_id').val();
            let floorNumber = $('#floor_number').val();
            let unitType = $('#unit_type').val();
            let aprtNo = $('#aprt_no').val().trim();


            console.log(formType);
            console.log(userId);
            let resultPhone;
            let resultEmail;
            if (formType == 'add') {
                resultPhone = await checkDuplicate('phone', phone);
                resultEmail = await checkDuplicate('email', email);
            } else {
                resultPhone = await checkDuplicate('phone', phone, userId);
                resultEmail = await checkDuplicate('email', email, userId);
            }

            // Clear previous error messages
            $('.err').text('');

            // Validate name
            if (name === '') {
                $('#name').next('.err').text('Name is required');
                hasError = 1;
            }

            // Validate role
            if (role === '') {
                $('#role').next('.err').text('Role is required');
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

            // Validate society_id
            if (societyId === '' || isNaN(societyId)) {
                $('#society_id').next('.err').text('Society is required and must be a valid number');
                hasError = 1;
            }

            // Validate block_id
            if (blockId === '' || isNaN(blockId)) {
                $('#block_id').next('.err').text('Block is required and must be a valid number');
                hasError = 1;
            }

            // Validate floor_number
            if (floorNumber === '' || isNaN(floorNumber)) {
                $('#floor_number').next('.err').text('Floor is required and must be a valid number');
                hasError = 1;
            }

            // Validate unit_type
            if (unitType === '') {
                $('#unit_type').next('.err').text('Unit Type is required');
                hasError = 1;
            }

            // Validate aprt_no
            if (aprtNo === '') {
                $('#aprt_no').next('.err').text('Apartment Number is required');
                hasError = 1;
            }

            // Return 1 if error is found, otherwise return 0
            return hasError;
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
        $(document).ready(function() {
            $('body').on('click', '.edit', function() {
                $('.err').text('');
                // disable outside click + exc press
                $('#addMemberModal').modal({
                    backdrop: 'static',
                    keyboard: false
                })
                // change modal heading
                $('#modalHeadTxt').text('Edit Society');

                const memberId = $(this).attr('id');

                $.ajax({
                    url: "{{ route($thisModule . '.member.edit', ['id' => ':memberId']) }}"
                        .replace(':memberId', memberId),
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        data = res.data;
                        if (res.status == 'error') {
                            toastr[res.status](res.message);
                        }
                        $('#addMemberForm').attr('action',
                            '{{ route($thisModule . '.member.update', ['id' => '__ID__']) }}'
                            .replace('__ID__', data.id));
                        $('#submitAddMemberForm').attr('data-formtype', 'edit');

                        //feed #addMemberForm form data by jq below
                        $('#id').val(data.user_id);
                        $('#name').val(data.name);
                        $('#role').val(data.role);
                        $('#phone').val(data.phone);
                        $('#email').val(data.email);
                        $('#aprt_no').val(data.aprt_no);
                        $('#society_id').val(data.society_id).trigger('change');

                        // Call showBlocks(s) and use a callback to ensure it completes first
                        showBlocks(data.society_id, function() {
                            // After showBlocks is done, set the other values
                            $('#block_id').val(data.block_id).trigger('change');
                            $('#floor_number').val(data.floor_number).trigger('change');
                            $('#unit_type').val(data.unit_type).trigger('change');
                        });

                        $('#addMemberModal').modal('show');
                    },
                    error: function(xhr, status, error) {
                        toastr.error('Unable to process the data');
                        console.error('Unable to process the data', error);
                    }
                });
            });
        });
    </script>
    {{-- delete submit --}}
    <script>
        $(document).ready(function() {
            $('.delete').on('click', function() {
                var memberId = $(this).data('id');
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
                            url: "{{ route($thisModule . '.member.delete', ['id' => ':memberId']) }}"
                                .replace(':memberId', memberId),
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken
                            },
                            success: function(response) {
                                toastr[response.status](response.message);
                                localStorage.setItem('activeTab', '#tab3-tab');
                                setTimeout(function() {
                                    location.reload();
                                }, 2000);
                            },
                            error: function(xhr) {
                                toastr.error('Failed please try again.');
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
