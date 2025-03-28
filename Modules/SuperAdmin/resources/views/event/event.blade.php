@extends($thisModule . '::layouts.master')

@section('title', 'Events')

@section('content')
    <div class="right_main_body_content members_page ">
        <div class="head_content">
            <div class="left_head">
                <h2>Events</h2>
            </div>

            <button type="button" class="bg_theme_btn" id="addEventModalOpen" data-bs-toggle="modal"
                data-bs-target="#addEventModal">
                <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M13 8H8V13C8 13.55 7.55 14 7 14C6.45 14 6 13.55 6 13V8H1C0.45 8 0 7.55 0 7C0 6.45 0.45 6 1 6H6V1C6 0.45 6.45 0 7 0C7.55 0 8 0.45 8 1V6H13C13.55 6 14 6.45 14 7C14 7.55 13.55 8 13 8Z"
                        fill="white" />
                </svg>
                Add Event
            </button>
        </div>
        <div class="custom_table_wrapper">

            <div class="filter_table_head visitor-sec">
                <div class="search_wrapper search-members-gstr">
                    <form method="GET" id="searchForm">

                        <div class="input-group">
                            <div class="filter-box">
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
                    {{-- <h2>Events Listing </h2> --}}

                </div>
            </div>
            <div class="table-responsive visitors-table">
                <table width="100%" cellpadding="0" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Event Title</th>
                            <th>Date </th>
                            <th>Time </th>
                            <th>Description</th>
                            <th class="text-center">status </th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $sl = 0;
                        @endphp
                        @if ($events && !$events->isEmpty())
                            @foreach ($events as $event)
                                @php
                                    $sl++;
                                    $eventDateTime = \Carbon\Carbon::createFromFormat(
                                        'Y-m-d H:i:s',
                                        $event->date . ' ' . $event->time,
                                        'Asia/Kolkata'
                                    )->startOfMinute(); // Ensures comparison is done at minute level

                                    $currentDateTime = \Carbon\Carbon::now('Asia/Kolkata')->startOfMinute(); // Ensures same precision

                                    if ($eventDateTime->greaterThan($currentDateTime)) { // More accurate than isFuture()
                                        $stts_text = 'Upcoming';
                                        $stts_class = 'up-status-btn';
                                    } else {
                                        $stts_text = 'Expired';
                                        $stts_class = 'exp-status-btn';
                                    }
                                @endphp
                                <tr>
                                    <td class="event-user"><img width="50" src="{{ $event->image }}" alt="">
                                    </td>
                                    <td>{{ $event->title }}</td>
                                    <td>{{ \Carbon\Carbon::parse($event->date)->format('d M Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($event->time)->format('g:i A') }}</td>
                                    <td>
                                        @if (strlen($event->description) > 40)
                                            {{ substr($event->description, 0, 37) . '...' }}
                                        @else
                                            {{ $event->description }}
                                        @endif
                                    </td>
                                    <td class="{{ $stts_class }}"><button>{{ $stts_text }}</button></td>
                                    <td class="text-center">
                                        <div class="actions">
                                            <a class="edit edit-icon" href="javascript:void(0)" id="{{ $event->id }}">
                                                <img src="{{ url($thisModule) }}/img/edit.png" alt="edit">
                                            </a>

                                            <a class="view"
                                                href="{{ route($thisModule . '.event.details', ['id' => $event->id]) }}">
                                                <img src="{{ url($thisModule) }}/img/eye.png" alt="view">
                                            </a>
                                            <a class="delete delete-icon" href="javascript:void(0)"
                                                data-id="{{ $event->id }}">
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
                            Showing {{ $events->firstItem() }} to {{ $events->lastItem() }} of
                            {{ $events->total() }} results
                        </div>
                        <div>
                            {{ $events->links('vendor.pagination.bootstrap-5') }} {{-- Bootstrap 5 pagination view --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Modal -->
    <div class="modal fade custom_Modal" id="addEventModal" tabindex="-1" aria-labelledby="addEventModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title text-white fs-5" id="modalHeadTxt">Create an Event </h1>
                    {{-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> --}}
                </div>
                <div class="modal-body">
                    <div class="custom_form">
                        <form method="POST" action="{{ route($thisModule . '.event.store') }}" id="addEventForm"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col">
                                    <div class="d-none">
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
                                    <div class="form-group">
                                        <label for="">Event Title</label>
                                        <input type="text" name="title" id="title" class="form-control">
                                        <span class="text-danger err"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col">
                                    <div class="date-fild">
                                        <label for="">Sub Title</label>
                                        <input type="text" name="sub_title" id="sub_title" class="form-control">
                                        <span class="text-danger err"></span>
                                    </div>
                                    <div class="date-fild">
                                        <label for="">Image</label>
                                        <input type="file" name="image" id="image"
                                            accept="image/png, image/jpeg, image/gif" class="form-control">
                                        <span class="text-danger err"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col">
                                    <div class="date-fild">
                                        <label for="">Select Date</label>
                                        <input type="date" name="date" id="date" class="form-control">
                                        <span class="text-danger err"></span>
                                    </div>
                                    <div class="date-fild">
                                        <label for="">Select Time</label>
                                        <input type="time" name="time" id="time" class="form-control">
                                        <span class="text-danger err"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col">
                                    <label for="">Event Description</label>
                                    <textarea name="description" id="description" style="margin-bottom: 16px;" class="form-control"></textarea>
                                    <span class="text-danger err"></span>
                                </div>
                            </div>

                            <div class="save-close-btn">
                                <button type="button" class="border_theme_btn close-btn cancel_btn"
                                    data-bs-dismiss="modal">Close</button>
                                <button type="button" data-formtype="add" class="bg_theme_btn"
                                    id="submitAddEventForm">Submit
                                </button>
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
        $(".members_page").on('click', '#addEventModalOpen', function(event) {
            // set modal form
            $('#addEventForm').attr('action',
                '{{ route($thisModule . '.event.store') }}'
            );
            $('#submitAddEventForm').attr('data-formtype', 'add');
            $('#modalHeadTxt').text('Add Event');
            $('#submitAddEventForm').text('Submit');

            // reset form data
            $('#addEventForm').find(
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
            $('#addEventModal').modal({
                backdrop: 'static',
                keyboard: false
            });
            // check form1 validation and switch to next form2
            $(".modal").on('click', '#submitAddEventForm', async function(event) {
                event.preventDefault();
                // loader add
                $('#loader').css('width', '50%');
                $('#loader').fadeIn();
                $('#blockOverlay').fadeIn();

                // let formType = $('#submitAddEventForm').data('formtype');
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

                    $('#addEventForm').submit();
                } else {
                    //on modal-cancel relaod page to show fresh updated data
                    $('#addEventForm').find('.cancel_btn').attr('onclick',
                        'window.location.reload()');
                    // let formData = $('#addEventForm').serialize();
                    let formData = new FormData($('#addEventForm')[0]);

                    $.ajax({
                        url: $('#addEventForm').attr('action'),
                        method: 'POST',
                        data: formData,
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        },
                        processData: false, // Prevent jQuery from automatically converting data
                        contentType: false, // Allow FormData to set the correct content type
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
            let sub_title = $('#sub_title').val().trim();
            let description = $('#description').val().trim();
            let date = $('#date').val().trim();
            let time = $('#time').val().trim();
            $('.err').text('');

            // Validate name
            if (title === '') {
                $('#title').siblings('.err').text('Title is required');
                hasError = 1;
            }

            if (sub_title === '') {
                $('#sub_title').siblings('.err').text('Sub Title is required');
                hasError = 1;
            }else{
                if(sub_title.length > 180){
                    $('#sub_title').siblings('.err').text('Sub Title should not exceed 180 characters.');
                    hasError = 1;
                }
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

            const input = $('#image');
            // const file = input.files[0];
            const file = input[0].files[0]; // Get the selected file
            const maxSize = 5 * 1024 * 1024; // 5MB in bytes
            let allowedTypes = ['image/png', 'image/jpeg', 'image/gif'];

            $('#image').siblings('.err').text('');

            // Check if a file is selected
            if (!file) {
                if (formType == 'add') {

                    $('#image').siblings('.err').text('Please select a file.');
                    hasError = 1;
                }
            } else {
                // Check file type
                if (!allowedTypes.includes(file.type)) {
                    $('#image').siblings('.err').text('Only PNG, JPEG, and GIF files are allowed.');
                    input.val(''); // Clear the input
                    hasError = 1;
                }

                // Check file size
                if (file.size > maxSize) {
                    $('#image').siblings('.err').text('Max size is 5MB.');
                    input.val(''); // Clear the input
                    hasError = 1;
                }
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

                // $('#addEventForm')[0].reset();
                $('#addEventForm').find(
                    'input:not([name="_token"],[name^="unit_type"],[name^="created_by"]), select, textarea'
                ).each(
                    function() {
                        $(this).val('');
                    });

                $('#addEventForm select').each(function() {
                    $(this).prop('selectedIndex', 0); // Select the first option
                });
                // disable outside click + exc press
                $('#addEventModal').modal({
                    backdrop: 'static',
                    keyboard: false
                })
                // change modal heading
                $('#modalHeadTxt').text('Edit Event');
                $('#submitAddEventForm').text('Update');

                const eventId = $(this).attr('id');

                $.ajax({
                    url: "{{ route($thisModule . '.event.edit', ['id' => ':eventId']) }}"
                        .replace(':eventId', eventId),
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
                        $('#addEventForm').attr('action',
                            '{{ route($thisModule . '.event.update', ['id' => '__ID__']) }}'
                            .replace('__ID__', data.id));
                        $('#submitAddEventForm').attr('data-formtype', 'edit');

                        //feed #addEventForm form data by jq below
                        $('#id').val(data.id);
                        $('#society_id').val(data.society_id);
                        $('#title').val(data.title);
                        $('#sub_title').val(data.sub_title);
                        $('#description').val(data.description);
                        $('#date').val(data.date);
                        $('#time').val(data.time);

                        //loader removed
                        $('#loader').css('width', '100%');
                        $('#loader').fadeOut();
                        $('#blockOverlay').fadeOut();

                        $('#addEventModal').modal('show');
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
                var eventId = $(this).data('id');
                console.log(eventId);

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
                            url: "{{ route($thisModule . '.event.delete', ['id' => ':eventId']) }}"
                                .replace(':eventId', eventId),
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
@endpush
