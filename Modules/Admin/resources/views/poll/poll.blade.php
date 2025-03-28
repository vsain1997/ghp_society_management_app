@extends($thisModule . '::layouts.master')

@section('title', 'Poll')

@section('content')
    <div class="right_main_body_content members_page ">
        <div class="add-polls-btn-box">

            @can('poll.create')
                <button type="button" class="add-polls-btn" id="addPollModalOpen" data-bs-toggle="modal"
                    data-bs-target="#addPollModal">
                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M13 8H8V13C8 13.55 7.55 14 7 14C6.45 14 6 13.55 6 13V8H1C0.45 8 0 7.55 0 7C0 6.45 0.45 6 1 6H6V1C6 0.45 6.45 0 7 0C7.55 0 8 0.45 8 1V6H13C13.55 6 14 6.45 14 7C14 7.55 13.55 8 13 8Z"
                            fill="white" />
                    </svg>
                    Add Poll

                </button>
            @endcan
        </div>
        <div class="custom_table_wrapper">

            <div class="filter_table_head visitor-sec">
                <div class="search_wrapper search-members-gstr">
                    <form action="{{ route($thisModule . '.poll.index') }}" method="GET">
                        {{-- @csrf --}}
                        <div class="input-group">
                            <input type="hidden" name="sid" value="{{ session('__selected_society__') }}">
                            <div class="filter-box">
                            <div class="filter-secl">

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
                    {{-- <h2>Polls Listing </h2> --}}

                </div>
            </div>
            <div class="table-responsive visitors-table">
                <table width="100%" cellpadding="0" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Title </th>
                            <th class="text-center">Total Votes </th>
                            <th class="text-center">Top Voted</th>
                            <th class="text-center">End Date </th>
                            <th class="text-center">Status </th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $sl = 0;
                        @endphp
                        @if ($polls && !$polls->isEmpty())
                            @foreach ($polls as $poll)
                                @php
                                    $sl++;
                                    $pollDateTime = \Carbon\Carbon::parse(
                                        $poll->end_date . ' 23:59:59',
                                        'Asia/Kolkata',
                                    );
                                    $currentDateTime = \Carbon\Carbon::now('Asia/Kolkata');

                                    if ($pollDateTime->isFuture()) {
                                        $stts_text = 'Ongoing';
                                        $stts_class = 'up-status-btn';
                                    } else {
                                        $stts_text = 'Closed';
                                        $stts_class = 'exp-status-btn';
                                    }
                                @endphp
                                <tr>
                                    <td>{{ $poll->title }}</td>

                                    <td class="text-center">{{ $poll->votes_count }}</td>
                                    <td class="text-center">
                                        @if ($poll->options->isNotEmpty() && $poll->votes_count > 0)
                                            <div>
                                                <p><strong>Option:</strong>
                                                    {{ $poll->options->first()->option_text }}</p>
                                                <p><strong>Votes:</strong> {{ $poll->options->first()->votes_count }}</p>
                                            </div>
                                        @else
                                            {{ '-' }}
                                        @endif
                                    </td>
                                    <td class="text-center">{{ \Carbon\Carbon::parse($poll->end_date)->format('d M Y') }}
                                    </td>
                                    <td class="text-center {{ $stts_class }}"><button>{{ $stts_text }}</button></td>
                                    <td class="text-center">
                                        <div class="actions">
                                            @can('poll.edit')
                                                <a class="edit edit-icon" href="javascript:void(0)" id="{{ $poll->id }}">
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

                                            <a class="view"
                                                href="{{ route($thisModule . '.poll.details', ['id' => $poll->id]) }}">
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
                                            @can('poll.delete')
                                                <a class="delete delete-icon" href="javascript:void(0)"
                                                    data-id="{{ $poll->id }}">
                                                    <svg width="16" height="17" viewBox="0 0 16 17" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <g clip-path="url(#clip0_193_2091)">
                                                            <path d="M2 4.68158H3.33333H14" stroke="#C90202"
                                                                stroke-width="1.33333" stroke-linecap="round"
                                                                stroke-linejoin="round" />
                                                            <path
                                                                d="M12.6673 4.68157V14.0149C12.6673 14.3685 12.5268 14.7077 12.2768 14.9577C12.0267 15.2078 11.6876 15.3482 11.334 15.3482H4.66732C4.3137 15.3482 3.97456 15.2078 3.72451 14.9577C3.47446 14.7077 3.33398 14.3685 3.33398 14.0149V4.68157M5.33398 4.68157V3.34824C5.33398 2.99462 5.47446 2.65548 5.72451 2.40543C5.97456 2.15538 6.3137 2.01491 6.66732 2.01491H9.33398C9.68761 2.01491 10.0267 2.15538 10.2768 2.40543C10.5268 2.65548 10.6673 2.99462 10.6673 3.34824V4.68157"
                                                                stroke="#C90202" stroke-width="1.33333" stroke-linecap="round"
                                                                stroke-linejoin="round" />
                                                            <path d="M6.66602 8.01491V12.0149" stroke="#C90202"
                                                                stroke-width="1.33333" stroke-linecap="round"
                                                                stroke-linejoin="round" />
                                                            <path d="M9.33398 8.01491V12.0149" stroke="#C90202"
                                                                stroke-width="1.33333" stroke-linecap="round"
                                                                stroke-linejoin="round" />
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
                                <td colspan="6" class="text-center"> No Data Found </td>
                            </tr>
                        @endif


                    </tbody>
                </table>
                <div class="table_bottom_box">
                    {{-- Pagination Links --}}
                    <div class="d-flex justify-content-between p-2 mt-2 mb-2">
                        <div>
                            Showing {{ $polls->firstItem() }} to {{ $polls->lastItem() }} of
                            {{ $polls->total() }} results
                        </div>
                        <div>
                            {{ $polls->links('vendor.pagination.bootstrap-5') }} {{-- Bootstrap 5 pagination view --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Modal -->
    <div class="modal fade create-polls" id="addPollModal" tabindex="-1" aria-labelledby="addPollModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5"><span id="modalHeadTxt">Create Poll</span></h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route($thisModule . '.poll.store') }}" id="addPollForm">
                        @csrf
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
                        <div>
                            <label for="">Poll Title</label>
                            <input type="text" name="title" id="title" required>
                            <span class="text-danger err"></span>
                        </div>
                        <div>
                            <label for="">End Date</label>
                            <input type="date" name="end_date" id="end_date" required>
                            <span class="text-danger err"></span>
                        </div>
                        <label for="">Options (Minimum 2, Maximum 10)</label>
                        <div id="options_block" class="option-put d-block">
                            <div class="d-flex option-row" style="width: 100%;" data-sl="1">
                                <div class="form-group col-md-10">
                                    <input type="hidden" name="optionId[1]">
                                    <input type="text" name="options[1]">
                                    <span class="text-danger err"></span>
                                </div>

                                <div class="form-group">
                                    <a href="javascript:void(0)" class="ms-4 add-new-put add_new_option">
                                        <svg width="15" height="15" viewBox="0 0 15 15" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M6.39844 8.98828H0.398438V6.98828H6.39844V0.988281H8.39844V6.98828H14.3984V8.98828H8.39844V14.9883H6.39844V8.98828Z"
                                                fill="white" />
                                        </svg>
                                    </a>
                                </div>

                            </div>
                        </div>

                        <div class="save-close-btn">
                            <button type="button" class="close-btn cancel_btn" data-bs-dismiss="modal">Close</button>
                            <button type="submit" data-formtype="add" class="submint-btn"
                                id="submitAddPollForm">Publish</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('footer-script')
    <script>
        $(".members_page").on('click', '#addPollModalOpen', function(poll) {
            // set modal form
            $('#addPollForm').attr('action',
                '{{ route($thisModule . '.poll.store') }}'
            );
            $('#submitAddPollForm').attr('data-formtype', 'add');
            $('#modalHeadTxt').text('Create Poll');
            $('#submitAddPollForm').text('Submit');
            let defaultOption = `
            <div class="d-flex option-row" style="width: 100%;" data-sl="1">
                <div class="form-group col-md-10">
                    <input type="hidden" name="optionId[1]">
                    <input type="text" name="options[1]">
                    <span class="text-danger err"></span>
                </div>

                <div class="form-group">
                    <a href="javascript:void(0)" class="ms-4 add-new-put add_new_option">
                        <svg width="15" height="15" viewBox="0 0 15 15" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M6.39844 8.98828H0.398438V6.98828H6.39844V0.988281H8.39844V6.98828H14.3984V8.98828H8.39844V14.9883H6.39844V8.98828Z"
                                fill="white" />
                        </svg>
                    </a>
                </div>

            </div>`;
            $('#options_block').html(defaultOption);

            // $('.option-row').each(function() {
            //     if ($(this).find('.remove_option_row').length) {
            //         $(this).remove(); // Remove the entire option-row div
            //     }
            // });

            // reset form data
            $('#addPollForm').find(
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
            $('#addPollModal').modal({
                backdrop: 'static',
                keyboard: false
            });
            // check form1 validation and switch to next form2
            $(".modal").on('click', '#submitAddPollForm', async function(event) {
                event.preventDefault();
                // loader add
                $('#loader').css('width', '50%');
                $('#loader').fadeIn();
                $('#blockOverlay').fadeIn();

                // let formType = $('#submitAddPollForm').data('formtype');
                let formType = $('#id').val();
                if (formType > 0 && formType) {
                    formType = 'edit';
                } else {
                    formType = 'add';
                }
                // console.log('call', formType);

                let validationStatus = await validateForm(formType);
                if (validationStatus == 'displayNoMsg') {
                    $('#loader').css('width', '100%');
                    $('#loader').fadeOut();
                    $('#blockOverlay').fadeOut();
                    return false;
                } else if (validationStatus != 0) {
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

                    $('#addPollForm').submit();
                } else {
                    //on modal-cancel relaod page to show fresh updated data
                    $('#addPollForm').find('.cancel_btn').attr('onclick',
                        'window.location.reload()');
                    // let formData = $('#addPollForm').serialize();
                    let formData = new FormData($('#addPollForm')[0]);

                    $.ajax({
                        url: $('#addPollForm').attr('action'),
                        method: 'POST',
                        data: formData,
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        },
                        processData: false, // Prpoll jQuery from automatically converting data
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
            let title = $('#title').val().trim();
            let end_date = $('#end_date').val().trim();
            $('.err').text('');

            // Validate name
            if (title === '') {
                $('#title').siblings('.err').text('Title is required');
                hasError = 1;
            }

            if (end_date === '') {
                $('#end_date').siblings('.err').text('End Date is required');
                hasError = 1;
            }

            // Loop through all option inputs
            let filledCount = 0;
            $('input[name^="options["]').each(function() {
                const value = $(this).val().trim();

                if (value === '') {
                    $(this).next('.err').text('Required'); // Show error
                    hasError = 1;
                    return 'displayNoMsg';

                } else {
                    filledCount++;
                }
            });

            // Check if at least 2 options are filled
            if (filledCount < 2) {
                toastr.error('Please fill at least two options.');
                return 'displayNoMsg';
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

                // $('#addPollForm')[0].reset();
                $('#addPollForm').find(
                    'input:not([name="_token"],[name^="unit_type"],[name^="created_by"]), select, textarea'
                ).each(
                    function() {

                        $(this).val('');
                    });

                $('#addPollForm select').each(function() {
                    $(this).prop('selectedIndex', 0); // Select the first option
                });
                // disable outside click + exc press
                $('#addPollModal').modal({
                    backdrop: 'static',
                    keyboard: false
                })
                // change modal heading
                $('#modalHeadTxt').text('Edit Poll');
                $('#submitAddPollForm').text('Update');

                const pollId = $(this).attr('id');

                $.ajax({
                    url: "{{ route($thisModule . '.poll.edit', ['id' => ':pollId']) }}"
                        .replace(':pollId', pollId),
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
                        $('#addPollForm').attr('action',
                            '{{ route($thisModule . '.poll.update', ['id' => '__ID__']) }}'
                            .replace('__ID__', data.id));
                        $('#submitAddPollForm').attr('data-formtype', 'edit');

                        //feed #addPollForm form data by jq below
                        $('#id').val(data.id);
                        $('#society_id').val(data.society_id);
                        $('#title').val(data.title);
                        $('#end_date').val(data.end_date);

                        let options = data.options;
                        let opHtml = '';
                        let sl = 0;
                        options.forEach(function(option) {
                            sl++;
                            opHtml +=
                                `<div class="d-flex option-row" style="width: 100%;" data-sl="${sl}">
                                    <div class="form-group col-md-12">
                                        <input type="hidden" name="optionId[${sl}]" value="${option.id}" required>
                                        <input type="text" name="options[${sl}]" value="${option.option_text}" required>
                                        <span class="text-danger err"></span>
                                    </div>`;

                            if (sl == 1) {

                                opHtml += `
                                    <div class="form-group d-none">
                                        <a href="javascript:void(0)" class="ms-4 add-new-put add_new_option">
                                            <svg width="15" height="15" viewBox="0 0 15 15" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M6.39844 8.98828H0.398438V6.98828H6.39844V0.988281H8.39844V6.98828H14.3984V8.98828H8.39844V14.9883H6.39844V8.98828Z"
                                                    fill="white" />
                                            </svg>
                                        </a>
                                    </div>`;
                            } else {
                                opHtml += `
                            <div class = "form-group d-none" >
                                <a href = "javascript:void(0)"
                                class = "ms-4 remove-option remove_option_row" >
                                    <svg width = "15"
                                    height = "15"
                                    viewBox = "0 0 15 15"
                                    fill = "none"
                                    xmlns = "http://www.w3.org/2000/svg" >
                                        <path d =
                                        "M0.398438 7.48828H14.3984V8.98828H0.398438V7.48828Z"
                                    fill = "white" />
                                        </svg>
                                </a>
                            </div>`;
                            }

                            opHtml += `</div>`;
                        });

                        jQuery('#options_block').html(opHtml);

                        //loader removed
                        $('#loader').css('width', '100%');
                        $('#loader').fadeOut();
                        $('#blockOverlay').fadeOut();

                        $('#addPollModal').modal('show');
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
                var pollId = $(this).data('id');
                console.log(pollId);

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
                            url: "{{ route($thisModule . '.poll.delete', ['id' => ':pollId']) }}"
                                .replace(':pollId', pollId),
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
    <script>
        jQuery(document).on('click', '.add_new_option', function() {

            let sl = Number(jQuery('#options_block').children().last().data('sl'));
            let totalChildren = jQuery('#options_block').children().length;
            console.log("totalChildren", totalChildren);

            if (totalChildren > 9) {
                toastr.error('You can\'t add more than 10 options');
                return false;
            }

            if (typeof sl === 'undefined' || sl === null || isNaN(sl)) {
                sl = 1;
            } else {
                sl = sl + 1;
            }

            let option_html = `
            <div class="d-flex option-row" style="width: 100%;" data-sl="${sl}">
                <div class="form-group col-md-10">
                    <input type="hidden" name="optionId[${sl}]">
                    <input type="text" name="options[${sl}]" required>
                    <span class="text-danger err"></span>
                </div>

                <div class="form-group">
                    <a href="javascript:void(0)" class="ms-4 remove-option remove_option_row">
                        <svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M0.398438 7.48828H14.3984V8.98828H0.398438V7.48828Z" fill="white" />
                        </svg>

                    </a>
                </div>

            </div>`;

            jQuery('#options_block').append(option_html);
        });

        $("#options_block").on('click', '.remove_option_row', function() {
            var $this = $(this);
            Swal.fire({
                title: 'Are you sure?',
                text: "This action cannot be undone!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $this.closest('.option-row').remove();
                    Swal.fire({
                        title: 'Deleted!',
                        text: 'The Option has been removed.',
                        icon: 'success',
                        showConfirmButton: false,
                        timer: 1500
                    });
                }
            });
        });
    </script>
@endpush
