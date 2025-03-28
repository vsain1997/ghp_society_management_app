@extends($thisModule . '::layouts.master')

@section('title', 'SOS')

@section('content')
    <div class="right_main_body_content members_page">
        <div class="head_content">
            <div class="left_head">
                <h2>SOS</h2>
            </div>
            <!-- Button trigger modal -->
        </div>
        <div class="custom_table_wrapper">
            <div class="filter_table_head visitor-sec">
                <div class="search_wrapper search-members-gstr">
                    <form method="GET" id="searchForm">

                        <div class="input-group">
                            <div class="filter-box">
                                <div class="filter-secl">
                                    <label for="status">Status</label>
                                    <select name="status" class="form-control">
                                        <option value="" >All</option>
                                        <option value="new" {{ request('status') == 'new' ? 'selected' : '' }}>New
                                        </option>
                                        <option value="acknowledged" {{ request('status') == 'acknowledged' ? 'selected' : '' }}>
                                            Acknowledged
                                        </option>
                                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>
                                            Cancelled
                                        </option>
                                    </select>
                                </div>
                                <div class="filter-secl">
                                    <label for="from_date">From Date</label>
                                    <input type="date" class="form-control" id="from_date" name="from_date"
                                        value="{{ request('from_date') }}">
                                    <span class="text-danger" id="from_date_error"></span>
                                </div>
                                <div class="filter-secl">
                                    <label for="to_date">To Date</label>
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
                    {{-- <h2>Emergency Listing </h2> --}}
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
                            <th class="text-center">Category</th>
                            <th class="text-center">Alert by </th>
                            <th class="text-center">Phone </th>
                            <th class="text-center">Area </th>
                            {{-- <th class="text-center">Block </th> --}}
                            {{-- <th class="text-center">Unit</th> --}}
                            {{-- <th class="text-center">Date </th> --}}
                            <th class="text-center">Time </th>
                            <th class="text-center">Description </th>
                            <th class="text-center">status </th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>@php
                        $sl = 0;
                    @endphp
                        @if ($soss && !$soss->isEmpty())
                            @foreach ($soss as $sos)
                                @php
                                    $sl++;

                                    $stts_text = Str::ucfirst($sos->status);
                                    if ($sos->status == 'new') {
                                        $stts_class = 'up-status-btn';
                                    }elseif ($sos->status == 'acknowledged') {
                                        $stts_class = 'green-status-btn';
                                    } elseif ($sos->status == 'cancelled') {
                                        $stts_class = 'exp-status-btn';
                                    }
                                @endphp
                                <tr>
                                    <td class="text-center">{{ $sos->sosCategory->name }}</td>
                                    <td class="text-center">{{ $sos->user->name }}</td>
                                    <td class="text-center">{{ $sos->user->phone }}</td>
                                    <td class="text-center">{{ $sos->area }}</td>

                                    <td class="text-center">{{ \Carbon\Carbon::parse($sos->date)->format('d M Y') }}

                                        {{-- </td>
                                    <td class="text-center"> --}}
                                        {{ \Carbon\Carbon::parse($sos->time)->format('g:i A') }}</td>
                                    <td class="text-center">
                                        @if (strlen($sos->description) > 50)
                                            {{ substr($sos->description, 0, 47) . '...' }}
                                        @else
                                            {{ $sos->description }}
                                        @endif
                                    </td>
                                    <td class="text-center {{ $stts_class }}"><button>{{ $stts_text }}</button></td>
                                    <td class="text-center">

                                        <div class="actions">

                                            <a class="call-icon" href="tel:{{ $sos->user->phone }}">
                                                <img src="{{ url($thisModule) }}/img/red-call.png" alt="call">
                                            </a>

                                            <a class="view"
                                                href="{{ route($thisModule . '.sos.details', ['id' => $sos->id]) }}"
                                                id="{{ $sos->id }}">
                                                <img src="{{ url($thisModule) }}/img/eye.png" alt="eye">
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="8" class="text-center"> No Data Found </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
                <div class="table_bottom_box">
                    {{-- Pagination Links --}}
                    <div class="d-flex justify-content-between p-2 mt-2 mb-2">
                        <div>
                            Showing {{ $soss->firstItem() }} to {{ $soss->lastItem() }} of
                            {{ $soss->total() }} results
                        </div>
                        <div>
                            {{ $soss->links('vendor.pagination.bootstrap-5') }} {{-- Bootstrap 5 pagination view --}}
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade custom_Modal" id="addNoticeModal" tabindex="-1" aria-labelledby="addNoticeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content ">
                <div class="modal-header">
                    <h3>Add Notice</h3>
                    {{-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> --}}
                </div>
                <div class="modal-body">
                    <div class="custom_form">
                        <form method="POST" action="{{ route($thisModule . '.notice.store') }}" id="addNoticeForm">
                            @csrf
                            <div class="row">
                                <div class="col d-none">
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
                                <div class="col">
                                    <div class="form-group">
                                        <label for="title">Notice Title</label>
                                        <input type="text" name="title" id="title" class="form-control">
                                        <span class="text-danger err"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <label for="date">Select Date</label>
                                        <input type="date" name="date" id="date" class="form-control">
                                        <span class="text-danger err"></span>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <label for="time">Select Time</label>
                                        <input type="time" name="time" id="time" class="form-control">
                                        <span class="text-danger err"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <label for="description">Description</label>
                                        <textarea name="description" id="description" class="form-control"></textarea>
                                        <span class="text-danger err"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col text-end">
                                    <button type="button" class="border_theme_btn cancel_btn"
                                        data-bs-dismiss="modal">Close</button>
                                    <button type="button" data-formtype="add" id="submitAddNoticeForm"
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
        $(".members_page").on('click', '#addNoticeModalOpen', function(event) {
            // set modal form
            $('#addNoticeForm').attr('action',
                '{{ route($thisModule . '.notice.store') }}'
            );
            $('#submitAddNoticeForm').attr('data-formtype', 'add');
            $('#modalHeadTxt').text('Add Notice');

            // reset form data
            $('#addNoticeForm').find(
                'input:not([name="_token"],[name^="society_id"],[name^="created_by"]), select, textarea').each(
                function() {
                    $(this).val('');
                });
            $('#society_id').val({{ session('__selected_society__') }});
            $('.err').text('');
        });
    </script>
    {{-- add + edit form validation and submit --}}
    <script>
        $(document).ready(function() {
            // disable modal outside click
            $('#addNoticeModal').modal({
                backdrop: 'static',
                keyboard: false
            });
            // check form1 validation and switch to next form2
            $(".modal").on('click', '#submitAddNoticeForm', async function(event) {
                event.preventDefault();
                // loader add
                $('#loader').css('width', '50%');
                $('#loader').fadeIn();
                $('#blockOverlay').fadeIn();

                // let formType = $('#submitAddNoticeForm').data('formtype');
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

                    $('#addNoticeForm').submit();
                } else {
                    //on modal-cancel relaod page to show fresh updated data
                    $('#addNoticeForm').find('.cancel_btn').attr('onclick',
                        'window.location.reload()');
                    let formData = $('#addNoticeForm').serialize();
                    $.ajax({
                        url: $('#addNoticeForm').attr('action'),
                        method: 'POST',
                        data: formData,
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        },
                        success: function(response) {
                            //loader removed
                            $('#loader').css('width', '100%');
                            $('#loader').fadeOut();
                            $('#blockOverlay').fadeOut();

                            toastr[response.status](response.message);
                        },
                        error: function(xhr, status, error) {
                            //loader removed
                            $('#loader').css('width', '100%');
                            $('#loader').fadeOut();
                            $('#blockOverlay').fadeOut();

                            toastr[response.status](response.message);
                        }
                    });

                    setTimeout(function() {
                        location.reload();
                    }, 2000);
                }
            });
        });

        //validate
        async function validateForm(formType) {
            let hasError = 0;
            // console.log('called', formType);

            // Assign form values to variables
            let societyId = $('#society_id').val();
            let title = $('#title').val().trim();
            let description = $('#description').val().trim();
            let date = $('#date').val().trim();
            let time = $('#time').val().trim();
            $('.err').text('');

            // Validate name
            if (title === '') {
                $('#title').siblings('.err').text('Title is required');
                hasError = 1;
            }

            if (description === '') {
                $('#description').siblings('.err').text('Description is required');
                hasError = 1;
            }

            if (date === '') {
                $('#date').siblings('.err').text('Date is required');
                hasError = 1;
            }

            if (time === '') {
                $('#time').siblings('.err').text('Time is required');
                hasError = 1;
            }

            // Validate society_id
            if (societyId === '' || isNaN(societyId)) {
                $('#society_id').siblings('.err').text('Society is required and must be a valid number');
                hasError = 1;
            }

            // Return 1 if error is found, otherwise return 0
            return hasError;
        }
    </script>
    {{-- show edit form --}}
    <script>
        $(document).ready(function() {
            $('body').on('click', '.edit', function() {
                $('.err').text('');
                // loader add
                $('#loader').css('width', '50%');
                $('#loader').fadeIn();
                $('#blockOverlay').fadeIn();

                // $('#addNoticeForm')[0].reset();
                $('#addNoticeForm').find(
                    'input:not([name="_token"],[name^="unit_type"],[name^="created_by"]), select, textarea'
                ).each(
                    function() {
                        $(this).val('');
                    });

                $('#addNoticeForm select').each(function() {
                    $(this).prop('selectedIndex', 0); // Select the first option
                });
                // disable outside click + exc press
                $('#addNoticeModal').modal({
                    backdrop: 'static',
                    keyboard: false
                })
                // change modal heading
                $('#modalHeadTxt').text('Edit Notice');

                const noticeId = $(this).attr('id');

                $.ajax({
                    url: "{{ route($thisModule . '.notice.edit', ['id' => ':noticeId']) }}"
                        .replace(':noticeId', noticeId),
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
                        $('#addNoticeForm').attr('action',
                            '{{ route($thisModule . '.notice.update', ['id' => '__ID__']) }}'
                            .replace('__ID__', data.id));
                        $('#submitAddNoticeForm').attr('data-formtype', 'edit');

                        //feed #addNoticeForm form data by jq below
                        $('#id').val(data.id);
                        $('#society_id').val(data.society_id);
                        $('#title').val(data.title);
                        $('#description').val(data.description);
                        $('#date').val(data.date);
                        $('#time').val(data.time);

                        //loader removed
                        $('#loader').css('width', '100%');
                        $('#loader').fadeOut();
                        $('#blockOverlay').fadeOut();

                        $('#addNoticeModal').modal('show');
                    },
                    error: function(xhr, status, error) {

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
        $(document).ready(function() {
            $('.delete').on('click', function() {
                var noticeId = $(this).data('id');
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
                            url: "{{ route($thisModule . '.notice.delete', ['id' => ':noticeId']) }}"
                                .replace(':noticeId', noticeId),
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken
                            },
                            success: function(response) {
                                toastr[response.status](response.message);
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
    {{-- status update --}}
    <script>
        $(document).on('change', '.statusOption', function() {

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


            var noticeId = $(this).data('id');
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
                        url: "{{ route($thisModule . '.notice.status.change', ['id' => ':noticeId', 'status' => ':toStatus']) }}"
                            .replace(':noticeId', noticeId).replace(':toStatus', toStatus),
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        },
                        success: function(response) {
                            toastr[response.status](response.message);
                            setTimeout(function() {
                                location.reload();
                            }, 2000);
                        },
                        error: function(xhr) {
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
