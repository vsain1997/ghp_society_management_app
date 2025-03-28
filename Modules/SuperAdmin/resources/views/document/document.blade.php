@extends($thisModule . '::layouts.master')

@section('title', 'Documents')

@section('content')
    <div class="right_main_body_content members_page">
        <div class="head_content">
            <div class="left_head">
                <h2> Document</h2>
                {{-- <p>Add or manage  of the society</p> --}}
            </div>
            <!-- Button trigger modal -->

            <button type="button" class="bg_theme_btn" id="addDocumentModalOpen" data-bs-toggle="modal"
                data-bs-target="#addDocumentModal">
                <svg width="14" height="15" viewBox="0 0 14 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M13.0005 8.5H8.00049V13.5C8.00049 14.05 7.55049 14.5 7.00049 14.5C6.45049 14.5 6.00049 14.05 6.00049 13.5V8.5H1.00049C0.450488 8.5 0.000488281 8.05 0.000488281 7.5C0.000488281 6.95 0.450488 6.5 1.00049 6.5H6.00049V1.5C6.00049 0.95 6.45049 0.5 7.00049 0.5C7.55049 0.5 8.00049 0.95 8.00049 1.5V6.5H13.0005C13.5505 6.5 14.0005 6.95 14.0005 7.5C14.0005 8.05 13.5505 8.5 13.0005 8.5Z"
                        fill="white" />
                </svg>
                Request Document
            </button>

        </div>
        <div class="custom_table_wrapper">
            <div class="filter_table_head visitor-sec">
                <div class="search_wrapper search-members-gstr">
                    <form method="GET">

                        <div class="input-group">
                            <div class="filter-box">
                                <div class="filter-secl">
                                    <label for="request_type">Request By</label>
                                    <select name="request_type" class="form-control">
                                        <option value="residents_request"
                                            {{ request('request_type') == 'residents_request' ? 'selected' : '' }}>Resident
                                        </option>
                                        <option value="admin_request"
                                            {{ request('request_type') == 'admin_request' ? 'selected' : '' }}>
                                            Admin
                                        </option>
                                        <option value="management_request"
                                            {{ request('request_type') == 'management_request' ? 'selected' : '' }}>
                                            Management
                                        </option>
                                    </select>
                                </div>
                                <div class="filter-secl">

                                    <label for="request_type">Status</label>
                                    <select name="status" class="form-control">
                                        <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>All
                                        </option>
                                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>
                                            Pending
                                        </option>
                                        <option value="received" {{ request('status') == 'received' ? 'selected' : '' }}>
                                            Uploaded
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="search-full-box">
                                <input type="search" name="search" id="search" placeholder="Search Title.."
                                    value="{{ request('search') }}">
                                <button type="submit" class="bg_theme_btn">
                                    Filter
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="right_filters">
                </div>
            </div>
            <div class="table-responsive">
                <table width="100%" cellpadding="0" cellspacing="0">
                    <thead>
                        <tr>
                            @if (!empty(request('request_type')))
                                @if (request('request_type') == 'residents_request')
                                    <th class="text-center">Request From</th>
                                    <th class="text-center">Phone</th>
                                @elseif (request('request_type') == 'management_request' || request('request_type') == 'admin_request')
                                    <th class="text-center">Request To</th>
                                    <th class="text-center">Phone</th>
                                @endif
                            @else
                                <th class="text-center">Request From</th>
                                <th class="text-center">Phone</th>
                            @endif
                            <th class="text-center">Requested Doc.</th>
                            <th class="text-center">Date</th>
                            <th class="text-center">Time</th>
                            <th class="text-center">Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>

                        @php
                            $i = ($documents->currentPage() - 1) * $documents->perPage() + 1;
                            $sl = 0;
                        @endphp
                        @if ($documents && !$documents->isEmpty())
                            @foreach ($documents as $request)
                                @php
                                    $sl++;
                                    $i++;

                                    if ($request->status == 'uploaded') {
                                        $stts_text = 'Uploaded';
                                        $stts_class = 'green-status-btn';
                                    } else {
                                        $stts_text = 'Pending';
                                        $stts_class = 'exp-status-btn';
                                    }
                                @endphp
                                <tr>
                                    @if (!empty(request('request_type')))
                                        @if (request('request_type') == 'residents_request')
                                            {{-- Request From --}}
                                            <td class="text-center">{{ $request->requestedBy->name }}</td>
                                            <td class="text-center">{{ $request->requestedBy->phone }}</td>
                                        @elseif (request('request_type') == 'management_request' || request('request_type') == 'admin_request')
                                            {{-- Request To --}}
                                            <td class="text-center">{{ $request->requestTo->name }}</td>
                                            <td class="text-center">{{ $request->requestTo->phone }}</td>
                                        @endif
                                    @else
                                        <td class="text-center">{{ $request->requestedBy->name }}</td>
                                        <td class="text-center">{{ $request->requestedBy->phone }}</td>
                                    @endif
                                    <td class="text-center">{{ $request->documentType->type }}</td>
                                    <td class="text-center">
                                        {{ \Carbon\Carbon::parse($request->created_at)->format('d M Y') }}</td>
                                    <td class="text-center">
                                        {{ \Carbon\Carbon::parse($request->created_at)->format('g:i A') }}</td>
                                    <td class="text-center {{ $stts_class }}"><button>{{ $stts_text }}</button></td>
                                    <td>
                                        <div class="actions">
                                            <a class="view"
                                                href="{{ route($thisModule . '.document.details', ['id' => $request->id]) }}"
                                                id="{{ $request->id }}">
                                                <img src="{{ url($thisModule) }}/img/eye.png" alt="view">
                                            </a>
                                            @if ($request->request_by_role == 'super_admin')
                                                <a href="javascript:void(0)" data-id="{{ $request->id }}"
                                                    class="delete delete-icon">
                                                    <img src="{{ url($thisModule) }}/img/delete.png" alt="view">
                                                </a>
                                            @endif
                                            @if ($request->status != 'uploaded' && ($request->request_by_role == 'resident' || $request->request_by_role == 'admin'))
                                                <a class="edit btn-sm upload-icon uplaod_file_btn"
                                                    style="border-radius: 50%" href="javascript:void(0)"
                                                    onclick="$('#fileInput{{ $i }}').click()"
                                                    id="chooseFileBtn{{ $i }}" id="{{ $request->id }}">
                                                    <i class="fa-solid fa-upload text-white"></i>
                                                </a>

                                                <!-- Hidden file input -->
                                                <input type="file" data-id="{{ $request->id }}" name="file[]"
                                                    id="fileInput{{ $i }}" style="display: none;" required
                                                    onchange="saveDocument(this, {{ $i }})" multiple>

                                                <!-- Error span -->
                                                <span id="errorSpan{{ $i }}"></span>
                                            @elseif (
                                                $request->status != 'uploaded' &&
                                                    $request->request_by_role == 'super_admin' &&
                                                    $request->request_to == auth()->id())
                                                <a class="edit upload-icon uplaod_file_btn" href="javascript:void(0)"
                                                    onclick="$('#fileInput{{ $i }}').click()"
                                                    id="chooseFileBtn{{ $i }}" id="{{ $request->id }}">
                                                    <i class="fa-solid fa-upload text-white"></i>
                                                </a>

                                                <!-- Hidden file input -->
                                                <input type="file" data-id="{{ $request->id }}" name="file[]"
                                                    id="fileInput{{ $i }}" style="display: none;" required
                                                    onchange="saveDocument(this, {{ $i }})" multiple>

                                                <!-- Error span -->
                                                <span id="errorSpan{{ $i }}"></span>
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
                            Showing {{ $documents->firstItem() }} to {{ $documents->lastItem() }} of
                            {{ $documents->total() }} results
                        </div>
                        <div>
                            {{ $documents->links('vendor.pagination.bootstrap-5') }} {{-- Bootstrap 5 pagination view --}}
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade custom_Modal" id="addDocumentModal" tabindex="-1" aria-labelledby="addDocumentModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content ">
                <div class="modal-header">
                    <h3 class="text-white" id="modalHeadTxt">Request Document</h3>
                    {{-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> --}}
                </div>
                <div class="modal-body">
                    <div class="custom_form">
                        <form method="POST" action="{{ route($thisModule . '.document.sendRequest') }}"
                            id="addDocumentForm">
                            @csrf
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
                                        <label for="request_to">Select Resident</label>
                                        <select name="request_to" id="request_to"
                                            class="residentList form-select form-control">
                                            <option value="">--select--</option>
                                            @if (!empty($societyResidents))
                                                @foreach ($societyResidents as $resident)
                                                    <option data-floor="{{ $resident->floor_number }}"
                                                        data-block="{{ $resident->block_name }}"
                                                        data-unitType="{{ $resident->unit_type }}"
                                                        data-aprtNo="{{ $resident->aprt_no }}"
                                                        data-phone="{{ $resident->phone }}"
                                                        data-name="{{ $resident->name }}"
                                                        value="{{ $resident->user_id }}">
                                                        {{ $resident->name }} | Property Number - {{ $resident->aprt_no }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <span class="text-danger err"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <label for="document_type_id">Select Document Type</label>
                                        <select name="document_type_id" id="document_type_id"
                                            class="documentList form-select form-control">
                                            <option value="">--select--</option>
                                            @if (!empty($docTypes))
                                                @foreach ($docTypes as $docType)
                                                    <option value="{{ $docType->id }}">
                                                        {{ $docType->type }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <span class="text-danger err"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <label for="subject">Subject</label>
                                        <input type="text" name="subject" id="subject" class="form-control">
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
                            <div class="save-close-btn">
                                <button type="button" class="border_theme_btn close-btn cancel_btn"
                                    data-bs-dismiss="modal">Close</button>
                                <button type="button" data-formtype="add" id="submitAddDocumentForm"
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
        $(".members_page").on('click', '#addDocumentModalOpen', function(event) {
            // set modal form
            $('#addDocumentForm').attr('action',
                '{{ route($thisModule . '.document.sendRequest') }}'
            );
            $('#submitAddDocumentForm').attr('data-formtype', 'add');
            $('#modalHeadTxt').text('Request Document');

            $('#resident-info').fadeOut();

            // reset form data
            $('#addDocumentForm').find(
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
            $('#addDocumentModal').modal({
                backdrop: 'static',
                keyboard: false
            });
            // check form1 validation and switch to next form2
            $(".modal").on('click', '#submitAddDocumentForm', async function(event) {
                event.preventDefault();
                // loader add
                $('#loader').css('width', '50%');
                $('#loader').fadeIn();
                $('#blockOverlay').fadeIn();

                // let formType = $('#submitAddDocumentForm').data('formtype');
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

                    $('#addDocumentForm').submit();
                }
            });
        });

        //validate
        async function validateForm(formType) {
            let hasError = 0;
            // console.log('called', formType);

            // Assign form values to variables
            let societyId = $('#society_id').val();
            let request_to = $('#request_to').val().trim();
            let document_type_id = $('#document_type_id').val().trim();
            let subject = $('#subject').val().trim();
            let description = $('#description').val().trim();
            $('.err').text('');

            // Validate name
            if (request_to === '') {
                $('#request_to').siblings('.err').text('Resident is required');
                hasError = 1;
            }

            if (document_type_id === '') {
                $('#document_type_id').siblings('.err').text('Document Type is required');
                hasError = 1;
            }

            if (subject === '') {
                $('#subject').siblings('.err').text('Subject is required');
                hasError = 1;
            }

            // if (description === '') {
            //     $('#description').siblings('.err').text('Description is required');
            //     hasError = 1;
            // }

            // Validate society_id
            if (societyId === '' || isNaN(societyId)) {
                $('#society_id').siblings('.err').text('Society is required and must be a valid number');
                hasError = 1;
            }

            // Return 1 if error is found, otherwise return 0
            return hasError;
        }
    </script>
    {{-- delete submit --}}
    <script>
        $(document).ready(function() {
            $('.delete').on('click', function() {
                var docrequestId = $(this).data('id');
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
                            url: "{{ route($thisModule . '.document.delete', ['id' => ':docrequestId']) }}"
                                .replace(':docrequestId', docrequestId),
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
        function saveDocument(element, index) {
            var csrf = document.head.querySelector('meta[name="csrf-token"]').content;

            // Access the input element directly
            var inputElement = element;

            // Hide the "Choose File" button
            // $('#chooseFileBtn' + index).hide();

            // Create FormData and append files
            const formData = new FormData();
            formData.append('_token', csrf);
            formData.append('document_id', $(element).data('id')); // Pass document ID

            // Iterate through files and append each one
            for (let i = 0; i < inputElement.files.length; i++) {
                formData.append('files[]', inputElement.files[i]);
            }

            // Show the progress bar for the specific file
            $('#loader').css('width', '50%');
            $('#loader').fadeIn();
            $('#blockOverlay').fadeIn();

            $.ajax({
                url: "{{ route($thisModule . '.document.uploadDocument') }}",
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                // xhr: function() {
                //     var xhr = new window.XMLHttpRequest();
                //     xhr.upload.addEventListener("progress", function(e) {
                //         if (e.lengthComputable) {
                //             var percentComplete = (e.loaded / e.total) * 100;
                //             $('#progress' + index + ' .progress-bar').css('width', percentComplete +
                //                 '%');
                //             $('#progress' + index + ' .progress-bar').html(percentComplete.toFixed(0) +
                //                 '% Complete');
                //         }
                //     }, false);
                //     return xhr;
                // },
                success: function(resp) {
                    console.log(resp);
                    // Handle the response here and update the UI accordingly
                    if (resp.status === true) {
                        toastr.success(resp.message);
                        // showSuccessMessage("Document uploaded successfully");
                    } else {
                        toastr.error(resp.message);
                        // showErrorMessage("Try again !!");
                    }
                },
                error: function(error) {
                    console.error(error);
                },
                complete: function() {
                    console.log('complete');
                    //loader removed
                    $('#loader').css('width', '100%');
                    $('#loader').fadeOut();
                    $('#blockOverlay').fadeOut();
                    location.reload();
                }
            });
        }
    </script>
    <script>
        $(document).ready(function() {
            // if (typeof $.fn.select2 === 'undefined') {
            //     console.error("Select2 is not loaded.");
            // } else {
            //     console.log("Select2 is loaded successfully.");
            // }




        });
    </script>

    <script>
        $(document).ready(function() {
            $('.documentList').select2({

                width: '100%',
                dropdownParent: $('.modal-content'),
                minimumResultsForSearch: 0 // Always show the search box
            });
            $('.residentList').select2({

                width: '100%',
                dropdownParent: $('.modal-content'),
                minimumResultsForSearch: 0 // Always show the search box
            });
            $('#request_to').change(function() {
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
        });
    </script>
@endpush
