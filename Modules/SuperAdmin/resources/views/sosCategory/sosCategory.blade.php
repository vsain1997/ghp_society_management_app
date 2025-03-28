@extends($thisModule . '::layouts.master')

@section('title', 'Sos Category')

@section('content')
    <div class="right_main_body_content members_page">
        <div class="head_content">
            <div class="left_head">
                <h2>Sos Category</h2>
                {{-- <p>Add or manage complaintCategorys of the society</p> --}}
            </div>
            <!-- Button trigger modal -->
            <button type="button" class="bg_theme_btn" id="addSosCategoryModalOpen" data-bs-toggle="modal"
                data-bs-target="#addComplaintCategoryModal">
                <svg width="14" height="15" viewBox="0 0 14 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M13.0005 8.5H8.00049V13.5C8.00049 14.05 7.55049 14.5 7.00049 14.5C6.45049 14.5 6.00049 14.05 6.00049 13.5V8.5H1.00049C0.450488 8.5 0.000488281 8.05 0.000488281 7.5C0.000488281 6.95 0.450488 6.5 1.00049 6.5H6.00049V1.5C6.00049 0.95 6.45049 0.5 7.00049 0.5C7.55049 0.5 8.00049 0.95 8.00049 1.5V6.5H13.0005C13.5505 6.5 14.0005 6.95 14.0005 7.5C14.0005 8.05 13.5505 8.5 13.0005 8.5Z"
                        fill="white" />
                </svg>
                Add SOS Category
            </button>
        </div>
        <div class="custom_table_wrapper">
            <div class="filter_table_head">
                {{-- <div class="search_wrapper search-members-gstr">
                    <form method="GET">
                        <div class="input-group">
                            <div class="search-full-box">
                                <input type="search" name="search" id="search" placeholder="Search Title.."
                                    value="{{ request('search') }}">
                                <button type="submit" class="bg_theme_btn">
                                    Filter
                                </button>
                            </div>
                        </div>
                    </form>
                </div> --}}
                <div class="right_filters">
                    {{-- <h2>SOS Category Listing </h2> --}}
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
                            <th>#</th>
                            <th>Image</th>
                            <th>Category</th>
                            <th>Date Added</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $sl = 0;
                        @endphp
                        @if ($sosCategory && !$sosCategory->isEmpty())
                            @foreach ($sosCategory as $sCategory)
                                @php
                                    $sl++;
                                @endphp
                                <tr>
                                    <td>{{ $sl }}</td>
                                    <td class="event-user"><img src="{{ $sCategory->image }}" alt="" width="80">
                                    </td>
                                    <td>{{ $sCategory->name }}</td>

                                    <td>{{ \Carbon\Carbon::parse($sCategory->created_at)->format('d M Y') }}</td>
                                    <td class="text-center">
                                        <div class="actions">
                                            <a class="edit edit-icon" href="javascript:void(0)" id="{{ $sCategory->id }}">
                                                <img src="{{ url($thisModule) }}/img/edit.png" alt="edit">
                                            </a>
                                            <a class="delete delete-icon" href="javascript:void(0)"
                                                data-id="{{ $sCategory->id }}">
                                                <img src="{{ url($thisModule) }}/img/delete.png" alt="view">
                                            </a>
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
                            Showing {{ $sosCategory->firstItem() }} to {{ $sosCategory->lastItem() }} of
                            {{ $sosCategory->total() }} results
                        </div>
                        <div>
                            {{ $sosCategory->links('vendor.pagination.bootstrap-5') }} {{-- Bootstrap 5 pagination view --}}
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade custom_Modal show" id="addComplaintCategoryModal" tabindex="-1"
        aria-labelledby="addComplaintCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content ">
                <div class="modal-header">
                    <h3 class="modal-title text-white fs-5" id="modalHeadTxt">Add SOS Category</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="custom_form">
                        <form method="POST" action="{{ route($thisModule . '.sos_category.store') }}"
                            id="addSosCategoryForm" enctype="multipart/form-data">
                            @csrf
                            <div class="col d-none">
                                <div class="form-group">
                                    <input type="hidden" name="id" id="id">
                                    <input type="hidden" name="created_by" value="{{ auth()->user()->id }}">
                                </div>
                            </div>
                            <div>
                                <label for="title">Category Name</label>
                                <input type="text" name="title" id="title">
                                <span class="text-danger err"></span>
                            </div>

                            <div class="date-time">
                                <label for="">Image</label>
                                <input type="file" name="image" id="image"
                                    accept="image/png, image/jpeg, image/gif">
                                <span class="text-danger err"></span>
                            </div>
                            <hr>
                            <p>Immediate Actions</p>
                            <div class="sos-add-remove" id="emer_block" class="">
                                <div class="option-row row" data-sl="1">
                                    <div class="form-group col-10">
                                        <input type="hidden" name="action_id[1]" value="">
                                        <input type="text" name="action_name[1]">
                                        <span class="text-danger err"></span>
                                    </div>
                                    <div class="col">

                                            <a href="javascript:void(0)" id="add_emer_row1" class="add_block_btn">+
                                            </a>

                                    </div>
                                </div>
                            </div>
                            <hr>
                            <p>Emergency Contacts</p>
                            <div class="sos-add-remove" id="emer_block2" >
                                <div class="row " data-sl="1">
                                    <div class="col-5">
                                        <label>Name</label>
                                        <input type="hidden" name="em_id[1]" value="">
                                        <input type="text" name="em_name[1]" class="form-control">
                                        <span class="text-danger err"></span>
                                    </div>
                                    <div class="col-5">
                                        <label>Phone</label>
                                        <input type="text" name="em_phone[1]" class="form-control">
                                        <span class="text-danger err"></span>
                                    </div>
                                    <div class="col-2 ">
                                        <a href="javascript:void(0)" id="add_emer_row2" class="add_block_btn">+
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="save-close-btn">
                                <button type="button" class="border_theme_btn close-btn cancel_btn"
                                    data-bs-dismiss="modal">Close</button>
                                <button type="button" data-formtype="add" id="submitAddSosCategoryForm"
                                    class="bg_theme_btn">Submit</button>
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
        $(".members_page").on('click', '#addSosCategoryModalOpen', function(event) {
            // set modal form
            $('#addSosCategoryForm').attr('action',
                '{{ route($thisModule . '.sos_category.store') }}'
            );
            $('#submitAddSosCategoryForm').attr('data-formtype', 'add');
            $('#modalHeadTxt').text('Add SOS Category');
            $('#submitAddSosCategoryForm').text('Submit');

            // action-------------
            // Select the first row and clear all its input values
            $('#emer_block .row').first().find('input').val('');
            // Remove all rows except the first one
            $('#emer_block .row').not(':first').remove();
            // contact-------------
            // Select the first row and clear all its input values
            $('#emer_block2 .row').first().find('input').val('');
            // Remove all rows except the first one
            $('#emer_block2 .row').not(':first').remove();

            // reset form data
            $('#addSosCategoryForm').find(
                'input:not([name="_token"],[name^="created_by"]), select, textarea').each(
                function() {
                    $(this).val('');
                });
            $('.err').text('');
        });
    </script>
    {{-- add + edit form validation and submit --}}
    <script>
        $(document).ready(function() {

            // disable modal outside click
            $('#addComplaintCategoryModal').modal({
                backdrop: 'static',
                keyboard: false
            });
            // check form1 validation and switch to next form2
            $(".modal").on('click', '#submitAddSosCategoryForm', async function(event) {
                event.preventDefault();
                // loader add
                $('#loader').css('width', '50%');
                $('#loader').fadeIn();
                $('#blockOverlay').fadeIn();

                // let formType = $('#submitAddSosCategoryForm').data('formtype');
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

                let flag2 = validate_emergency_actions();
                if (flag2) {
                    //loader removed
                    $('#loader').css('width', '100%');
                    $('#loader').fadeOut();
                    $('#blockOverlay').fadeOut();
                    return false;
                }
                let flag3 = validate_emergency_contacts();
                if (flag3) {
                    //loader removed
                    $('#loader').css('width', '100%');
                    $('#loader').fadeOut();
                    $('#blockOverlay').fadeOut();
                    return false;
                }

                //direct submit for add and ajax submit for edit
                if (formType == 'add') {
                    //loader removed
                    $('#loader').css('width', '100%');
                    $('#loader').fadeOut();
                    $('#blockOverlay').fadeOut();

                    $('#addSosCategoryForm').submit();
                } else {
                    //on modal-cancel relaod page to show fresh updated data
                    $('#addSosCategoryForm').find('.cancel_btn').attr('onclick',
                        'window.location.reload()');
                    // let formData = $('#addSosCategoryForm').serialize();
                    let formData = new FormData($('#addSosCategoryForm')[0]);
                    $.ajax({
                        url: $('#addSosCategoryForm').attr('action'),
                        method: 'POST',
                        data: formData,
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        },
                        processData: false,
                        contentType: false,
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
            $('.err').text('');

            // Validate name
            if (title === '') {
                $('#title').siblings('.err').text('Title is required');
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

        function validate_emergency_actions() {
            let setflag = 0;
            $('#emer_block .row').each(function() {
                let serial = $(this).data('sl');
                let hasError = false;

                // Validate Name
                let $action_name = $(`input[name="action_name[${serial}]"]`);
                let name = $action_name.val();
                let $nameError = $action_name.siblings('.err');
                let nameErrMsg_invalid = 'Invalid Entry';

                if (name.length > 0 && !isNaN(name)) {
                    setflag = 1;
                    hasError = true;
                    $action_name.siblings('.err').text(nameErrMsg_invalid);
                }
            });
            return setflag;
        }

        function validate_emergency_contacts() {
            let setflag = 0;
            $('#emer_block2 .row').each(function() {
                let serial = $(this).data('sl');
                let hasError = false;

                // Validate Name
                let $action_name = $(`input[name="em_name[${serial}]"]`);
                let name = $action_name.val();
                let $nameError = $action_name.siblings('.err');
                let nameErrMsg_invalid = 'Invalid Entry';

                // setflag = 0;
                // hasError = 0;

                if (name.length > 0 && !isNaN(name)) {
                    setflag = 1;
                    hasError = true;
                    $action_name.siblings('.err').text(nameErrMsg_invalid);
                }

                // validate phone number
                let $phoneInput = $(`input[name="em_phone[${serial}]"]`);
                let phone = $phoneInput.val();
                let $phoneError = $phoneInput.siblings('.err');

                let phoneErrMsg = 'Phone is required';
                let phoneErrMsg_invalid = 'Must be 10 digits';

                // Initialize flags
                // setflag = 0;
                // hasError = 0;

                // Validate phone
                if (name.length > 0 && isEmpty(phone, $phoneError, phoneErrMsg)) {
                    setflag = 1;
                    hasError = true;
                } else if (phone.length > 0 && isInvalidPhone(phone, $phoneError, phoneErrMsg_invalid)) {
                    setflag = 1;
                    hasError = true;
                }

            });

            return setflag;
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

                // action-------------
                // Select the first row and clear all its input values
                $('#emer_block .row').first().find('input').val('');
                // Remove all rows except the first one
                $('#emer_block .row').not(':first').remove();
                // contact-------------
                // Select the first row and clear all its input values
                $('#emer_block2 .row').first().find('input').val('');
                // Remove all rows except the first one
                $('#emer_block2 .row').not(':first').remove();
                // $('#addSosCategoryForm')[0].reset();
                $('#addSosCategoryForm').find(
                    'input:not([name="_token"],[name^="created_by"]), select, textarea'
                ).each(
                    function() {
                        $(this).val('');
                    });

                $('#addSosCategoryForm select').each(function() {
                    $(this).prop('selectedIndex', 0); // Select the first option
                });
                // disable outside click + exc press
                $('#addComplaintCategoryModal').modal({
                    backdrop: 'static',
                    keyboard: false
                })
                // change modal heading
                $('#modalHeadTxt').text('Edit SOS Category');
                $('#submitAddSosCategoryForm').text('Update');

                const complaintCategoryId = $(this).attr('id');

                $.ajax({
                    url: "{{ route($thisModule . '.sos_category.edit', ['id' => ':complaintCategoryId']) }}"
                        .replace(':complaintCategoryId', complaintCategoryId),
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
                        $('#addSosCategoryForm').attr('action',
                            '{{ route($thisModule . '.sos_category.update', ['id' => '__ID__']) }}'
                            .replace('__ID__', data.id));
                        $('#submitAddSosCategoryForm').attr('data-formtype', 'edit');

                        //feed #addSosCategoryForm form data by jq below
                        $('#id').val(data.id);
                        $('#title').val(data.name);
                        // -----------------------------
                        // Populate actions
                        let sl = 1; // Unique serial
                        data.emergency_details.actions.forEach(function(action) {
                            if (sl == 1) {
                                // Example to fill value for action_name[1]
                                $('input[name="action_id[1]"]').val(action.id);
                                $('input[name="action_name[1]"]').val(action.name);
                            } else {
                                let emerHtml = `
                                <div class="option-row row" data-sl="${sl}">
                                    <div class="form-group col-10">
                                        <input type="hidden" name="action_id[${sl}]" value="${action.id}">
                                        <input type="text" name="action_name[${sl}]" value="${action.name}" >
                                        <span class="text-danger err"></span>
                                        </div>
                                        <div class="col">

                                                <a href="javascript:void(0)" class=" remove_emer_row ">-</a>
                                                </div>
                                                </div>
                                                `;
                                $('#emer_block').append(emerHtml);
                            }
                            sl++;
                        });

                        // Populate contacts
                        sll = 1; // Reset serial for contacts
                        data.emergency_details.contacts.forEach(function(contact) {
                            if (sll == 1) {
                                // Example to fill value for action_name[1]
                                $('input[name="em_id[1]"]').val(contact.id);
                                $('input[name="em_name[1]"]').val(contact.name);
                                $('input[name="em_phone[1]"]').val(contact.phone);
                            } else {
                                let emerHtml2 = `
                                <div class="row" data-sl="${sll}">
                                    <div class="col-5">
                                        <input type="hidden" name="em_id[${sll}]" value="${contact.id}">
                                        <input type="text" name="em_name[${sll}]" value="${contact.name}" class="form-control">
                                        <span class="text-danger err"></span>
                                    </div>
                                    <div class="col-5">
                                        <input type="text" name="em_phone[${sll}]" value="${contact.phone}" class="form-control">
                                        <span class="text-danger err"></span>
                                    </div>
                                    <div class="col-2">
                                        <div class="form-group">
                                            <a href="javascript:void(0)" class=" remove_emer_row ">-</a>
                                        </div>
                                    </div>
                                </div>`;
                                $('#emer_block2').append(emerHtml2);
                            }
                            sll++;
                        });
                        // -----------------------------
                        //loader removed
                        $('#loader').css('width', '100%');
                        $('#loader').fadeOut();
                        $('#blockOverlay').fadeOut();

                        $('#addComplaintCategoryModal').modal('show');
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
                var complaintCategoryId = $(this).data('id');
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
                            url: "{{ route($thisModule . '.sos_category.delete', ['id' => ':complaintCategoryId']) }}"
                                .replace(':complaintCategoryId', complaintCategoryId),
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
        $(document).ready(function() {

            $(document).on('click', '#add_emer_row1', function() {

                let sl = Number($('#emer_block').children().last().data('sl'));

                if (typeof sl === 'undefined' || sl === null || isNaN(sl)) {
                    sl = 1;
                } else {
                    sl = sl + 1;
                }

                let emer_html = `
                <div class="option-row row" data-sl="${sl}">
                    <div class="form-group col-10">
                        <input type="hidden" name="action_id[${sl}]" value="">
                        <input type="text" name="action_name[${sl}]" >
                        <span class="text-danger err"></span>
                    </div>
                    <div class="col">

                            <a href="javascript:void(0)"
                                class=" remove_emer_row ">-
                            </a>

                    </div>
                </div>`;

                $('#emer_block').append(emer_html);
            });

            $("#emer_block").on('click', '.remove_emer_row', function() {
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
                        $this.closest('.row').remove();
                        Swal.fire({
                            title: 'Deleted!',
                            text: 'The Row has been removed.',
                            icon: 'success',
                            showConfirmButton: false,
                            timer: 1500
                        });
                    }
                });
            });
            // --------------------
            $(document).on('click', '#add_emer_row2', function() {

                let sl = Number($('#emer_block').children().last().data('sl'));

                if (typeof sl === 'undefined' || sl === null || isNaN(sl)) {
                    sl = 1;
                } else {
                    sl = sl + 1;
                }

                let emer_html2 = `
                    <div class="row" data-sl="${sl}">
                         <div class="col-5">
                            <input type="hidden" name="em_id[${sl}]" value="">
                            <input type="text" name="em_name[${sl}]" class="form-control">
                            <span class="text-danger err"></span>
                        </div>
                        <div class="col-5">
                            <input type="text" name="em_phone[${sl}]" class="form-control">
                            <span class="text-danger err"></span>
                        </div>
                        <div class="col-2">

                                <a href="javascript:void(0)"
                                    class="remove_emer_row ">-
                                </a>

                        </div>
                    </div>`;

                $('#emer_block2').append(emer_html2);
            });

            $("#emer_block2").on('click', '.remove_emer_row', function() {
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
                        $this.closest('.row').remove();
                        Swal.fire({
                            title: 'Deleted!',
                            text: 'The Row has been removed.',
                            icon: 'success',
                            showConfirmButton: false,
                            timer: 1500
                        });
                    }
                });
            });
        });
    </script>
@endpush
