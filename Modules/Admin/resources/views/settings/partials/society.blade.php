<div class="head_content">
    <div class="left_head">
        <h2>Society Management</h2>
        <p>Add or manage societies</p>
    </div>
    <!-- Button trigger modal -->
    <button type="button" id="addSocietyModalOpenBtn" class="bg_theme_btn" data-bs-toggle="modal"
        data-bs-target="#addSocietyModal">
        <svg width="14" height="15" viewBox="0 0 14 15" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path
                d="M13.0005 8.5H8.00049V13.5C8.00049 14.05 7.55049 14.5 7.00049 14.5C6.45049 14.5 6.00049 14.05 6.00049 13.5V8.5H1.00049C0.450488 8.5 0.000488281 8.05 0.000488281 7.5C0.000488281 6.95 0.450488 6.5 1.00049 6.5H6.00049V1.5C6.00049 0.95 6.45049 0.5 7.00049 0.5C7.55049 0.5 8.00049 0.95 8.00049 1.5V6.5H13.0005C13.5505 6.5 14.0005 6.95 14.0005 7.5C14.0005 8.05 13.5505 8.5 13.0005 8.5Z"
                fill="white" />
        </svg>
        Add Society
    </button>
</div>
<div class="custom_table_wrapper settings_table">
    <div class="filter_table_head">
        <div class="search_wrapper search-members-gstr">
            <form action="{{ route($thisModule . '.settings') }}" method="GET">
                {{-- @csrf --}}
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
                    <div class="filter-secl">
                        <select name="status" class="form-control">
                            <option value="none" disabled>--Choose Status--</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive
                            </option>
                        </select>
                    </div>
                    <div class="search-full-box">
                        <input type="search" name="society_name" id="search" placeholder="Search"
                            value="{{ request('society_name') }}">
                        <button type="submit" class="bg_theme_btn">
                            {{-- <svg width="24" height="25" viewBox="0 0 24 25" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M11.5 21.5C16.7467 21.5 21 17.2467 21 12C21 6.75329 16.7467 2.5 11.5 2.5C6.25329 2.5 2 6.75329 2 12C2 17.2467 6.25329 21.5 11.5 21.5Z"
                                            stroke="#fff" stroke-width="1.4" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                        <path d="M22 22.5L20 20.5" stroke="#fff" stroke-width="1.5" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                    </svg> --}}
                            Filter
                        </button>
                    </div>
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
            </button> --}}
            <button class="filterbtn d-none">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path d="M22 6.5H16" stroke="#020015" stroke-width="1.5" stroke-miterlimit="10"
                        stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M6 6.5H2" stroke="#020015" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round"
                        stroke-linejoin="round" />
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
            </button>

        </div>
    </div>
    <div class="table-responsive">
        <table width="100%" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th>Sl. No.</th>
                    <th>Name</th>
                    <th>Location</th>
                    <th>Total Units</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $sl = 0;
                @endphp

                @if ($societies && !$societies->isEmpty())
                    @foreach ($societies as $society)
                        @php
                            $sl++;
                        @endphp
                        <tr>
                            <td>{{ $sl }}</td>
                            <td>{{ $society->name }}</td>
                            <td>{{ $society->location }}</td>
                            <td>{{ (int) ($society->floors * $society->floor_units) }}</td>
                            <td>
                                <input type="hidden" name="statusVal" value="{{ parseStatus($society->status, 0) }}">
                                <div class="status_select">
                                    <select name="status" data-id="{{ $society->id }}"
                                        class="statusOption form-select">
                                        <option value="active" {{ parseStatus($society->status, 1) }}>Active
                                        </option>
                                        <option value="inactive" {{ parseStatus($society->status, 2) }}>In
                                            Active</option>
                                    </select>
                                </div>
                            </td>
                            <td>
                                <div class="actions">
                                    <a href="javascript:void(0)" id="{{ $society->id }}" class="edit">
                                        <img src="{{ url($thisModule) }}/img/edit.png" alt="edit">
                                    </a>
                                    <a href="{{ route($thisModule . '.society.details', ['id' => $society->id]) }}"
                                        id="{{ $society->id }}" class="view">
                                        <img src="{{ url($thisModule) }}/img/eye.png" alt="eye">
                                    </a>
                                    <a href="javascript:void(0)" data-id="{{ $society->id }}" class="delete">
                                        <img src="{{ url($thisModule) }}/img/delete.png" alt="delete">
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
                    Showing {{ $societies->firstItem() }} to {{ $societies->lastItem() }} of
                    {{ $societies->total() }} results
                </div>
                <div>
                    {{ $societies->links('vendor.pagination.bootstrap-5') }} {{-- Bootstrap 5 pagination view --}}
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Modal::add -->
<div class="modal fade custom_Modal" id="addSocietyModal" tabindex="-1" aria-labelledby="addSocietyModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content ">
            <div class="modal-header">
                <h3 id="modalHeadTxt">Add Society</h3>
                {{-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> --}}
            </div>
            <div class="modal-body">
                <div class="add_society_wrapper">
                    <ul class="nav" id="myTab" role="tablist">
                        <li class="nav-item active_item" role="presentation">
                            <button class="nav-link active" id="societyTab1-tab" data-bs-toggle="tab"
                                data-bs-target="#societyTab1" type="button" role="tab"
                                aria-controls="societyTab1" aria-selected="true">
                                <span>1</span>
                                <strong>Add Society</strong>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="societyTab2-tab" data-bs-toggle="tab"
                                data-bs-target="#societyTab2" type="button" role="tab"
                                aria-controls="societyTab2" aria-selected="false">
                                <span>2</span>
                                <strong>Add Units</strong>
                            </button>
                        </li>
                    </ul>
                    <form id="societyAddForm" action="{{ route($thisModule . '.society.store') }}" method="POST">
                        @csrf
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="societyTab1" role="tabpanel"
                                aria-labelledby="societyTab1-tab">
                                <div class="custom_form">
                                    <div class="form">
                                        <div class="row">
                                            <div class="col">
                                                <div class="form-group">
                                                    <label for="sname">Society Name</label>
                                                    <input type="text" name="sname" id="sname"
                                                        class="form-control">
                                                    <span id="snameErr" class="text-danger"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col">
                                                <div class="form-group">
                                                    <label for="location">Location</label>
                                                    <input type="text" name="location" id="location"
                                                        class="form-control">
                                                    <span id="locationErr" class="text-danger"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col">
                                                <div class="form-group">
                                                    <label for="statusSelect">Status</label>
                                                    <select name="statusSelect" id="statusSelect"
                                                        class="form-select form-control ">
                                                        <option value="active" selected>Active</option>
                                                        <option value="inactive">In active</option>
                                                    </select>
                                                    <span id="statusSelectErr" class="text-danger"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col">
                                                <div class="form-group">
                                                    <label for="floors">Number Of Floors</label>
                                                    <input type="text" name="floors" id="floors"
                                                        class="form-control">
                                                    <span id="floorsErr" class="text-danger"></span>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="form-group">
                                                    <label for="floorUnits">Units Per Floor</label>
                                                    <input type="text" name="floorUnits" id="floorUnits"
                                                        class="form-control">
                                                    <span id="floorUnitsErr" class="text-danger"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col">
                                                <div class="form-group">
                                                    <label for="assignAdmin">Assign Admin</label>
                                                    <select name="assignAdmin" id="assignAdmin"
                                                        class="form-select form-control ">
                                                        <option value="" selected>--select--</option>
                                                        <option value="" disabled>No admins available</option>

                                                        {{-- @foreach ($admins as $admin)
                                                            <option value="{{ $admin->id }}">{{ $admin->name }}
                                                            </option>
                                                        @endforeach --}}
                                                    </select>
                                                    <span id="assignAdminErr" class="text-danger"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <p>Emergency Contacts</p>
                                        <div id="emer_block" class="mb-4">
                                            <div class="row" data-sl="1">
                                                <div class="col">
                                                    <div class="form-group">
                                                        <label>Name</label>
                                                        <input type="text" name="emr_name[1]"
                                                            class="form-control">
                                                        <span class="text-danger err"></span>
                                                    </div>
                                                </div>
                                                <div class="col">
                                                    <div class="form-group">
                                                        <label>Designation</label>
                                                        <input type="text" name="emr_designation[1]"
                                                            class="form-control">
                                                        <span class="text-danger err"></span>
                                                    </div>
                                                </div>
                                                <div class="col">
                                                    <div class="form-group">
                                                        <label>Phone</label>
                                                        <input type="text" name="emr_phone[1]"
                                                            class="form-control">
                                                        <span class="text-danger err"></span>
                                                    </div>
                                                </div>
                                                <div class="col">
                                                    <div class="form-group">
                                                        <a href="javascript:void(0)" id="add_emer_row"
                                                            class="btn btn-primary mt-4">+
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col text-end">
                                                <button type="button" class="border_theme_btn cancel_btn"
                                                    data-bs-dismiss="modal">Close</button>
                                                <button type="button" data-type="add"
                                                    class="societyAddForm1 bg_theme_btn next_btn">Next</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="societyTab2" role="tabpanel"
                                aria-labelledby="societyTab2-tab">
                                <div class="block_wrapper">
                                    <div class="accordion" id="accordionBlock">
                                        <div class="accordion-item" data-serial="1">
                                            <h2 class="accordion-header" id="blockHeading1">
                                                <button class="accordion-button" type="button"
                                                    data-bs-toggle="collapse" data-bs-target="#blockCollapse1"
                                                    aria-expanded="true" aria-controls="blockCollapse1">
                                                    <span class="showBlockName"></span>
                                                </button>
                                            </h2>
                                            <div id="blockCollapse1" class="accordion-collapse collapse show"
                                                aria-labelledby="blockHeading1" data-bs-parent="#accordionBlock">
                                                <div class="accordion-body">
                                                    <div class="block_fields">
                                                        <div class="custom_form">
                                                            <div class="form">
                                                                <div class="row">
                                                                    <div class="col">
                                                                        <div class="form-group">

                                                                            <label>Block
                                                                                Name</label>
                                                                            <input type="text" name="bname[1]"
                                                                                class="form-control">

                                                                            <span class="text-danger err"></span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col">
                                                                        <div class="form-group">
                                                                            <label>Total
                                                                                Units</label>
                                                                            <input type="text" name="totalUnits[1]"
                                                                                class="form-control">

                                                                            <span class="text-danger err"></span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="blocks_table">
                                                            <h5>Units</h5>
                                                            <div class="table-responsive">
                                                                <table width="100%" cellpadding="0"
                                                                    cellspacing="0">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Unit Type</th>
                                                                            <th>Size (sq.ft) </th>
                                                                            <th>QTY</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <tr>
                                                                            <td>
                                                                                Plot
                                                                                <input type="hidden"
                                                                                    name="unit_type[1][]"
                                                                                    value="Plot">
                                                                                <br>
                                                                                <span class="text-danger err"></span>
                                                                            </td>
                                                                            <td>
                                                                                <input type="text"
                                                                                    name="unit_size[1][]"
                                                                                    class="block-field">
                                                                                <br>
                                                                                <span class="text-danger err"></span>
                                                                            </td>
                                                                            <td>
                                                                                <input type="number"
                                                                                    name="unit_qty[1][]"
                                                                                    class="block-field">
                                                                                <br>
                                                                                <span class="text-danger err"></span>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>
                                                                                Office
                                                                                <input type="hidden"
                                                                                    name="unit_type[1][]"
                                                                                    value="Office">
                                                                                <br>
                                                                                <span class="text-danger err"></span>
                                                                            </td>
                                                                            <td>
                                                                                <input type="text"
                                                                                    name="unit_size[1][]"
                                                                                    class="block-field">
                                                                                <br>
                                                                                <span class="text-danger err"></span>
                                                                            </td>
                                                                            <td>
                                                                                <input type="number"
                                                                                    name="unit_qty[1][]"
                                                                                    class="block-field">
                                                                                <br>
                                                                                <span class="text-danger err"></span>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>
                                                                                Flat
                                                                                <input type="hidden"
                                                                                    name="unit_type[1][]"
                                                                                    value="Flat">
                                                                                <br>
                                                                                <span class="text-danger err"></span>
                                                                            </td>
                                                                            <td>

                                                                                <input type="text"
                                                                                    name="unit_size[1][]"
                                                                                    class="block-field">
                                                                                <br>
                                                                                <span class="text-danger err"></span>
                                                                            </td>
                                                                            <td>

                                                                                <input type="number"
                                                                                    name="unit_qty[1][]"
                                                                                    class="block-field">
                                                                                <br>
                                                                                <span class="text-danger err"></span>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>
                                                                                Other
                                                                                <input type="hidden"
                                                                                    name="unit_type[1][]"
                                                                                    value="Other">
                                                                                <br>
                                                                                <span class="text-danger err"></span>
                                                                            </td>
                                                                            <td>
                                                                                <input type="text"
                                                                                    name="unit_size[1][]"
                                                                                    class="block-field">
                                                                                <br>
                                                                                <span class="text-danger err"></span>
                                                                            </td>
                                                                            <td>
                                                                                <input type="number"
                                                                                    name="unit_qty[1][]"
                                                                                    class="block-field">
                                                                                <br>
                                                                                <span class="text-danger err"></span>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td colspan="3">
                                                                                <button type="button"
                                                                                    class="deleteBlock btn btn-danger mt-2"
                                                                                    style="width:100%">Delete</button>
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="addBlock_section">
                                        <a href="javascript:void(0)" class="add_block_field"
                                            id="addNewBockErrChecker" data-error="1">
                                            <svg width="16" height="15" viewBox="0 0 16 15" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M8.83092 0.171115L7.16922 0.171114L7.16922 6.75899L0.581339 6.75899V8.42069L7.16922 8.42069L7.16922 15.0086L8.83092 15.0086L8.83092 8.42069L15.4188 8.42069V6.75899L8.83092 6.75899L8.83092 0.171115Z"
                                                    fill="#6459CC" />
                                            </svg>
                                            Add New Block
                                        </a>
                                    </div>
                                    <div class="block_actions">
                                        <div class="row">
                                            <div class="col text-end">
                                                <button type="button" class="border_theme_btn cancel_btn"
                                                    data-bs-dismiss="modal">Cancel</button>
                                                <button type="button" data-formtype="add" id="societyAddForm2"
                                                    class="bg_theme_btn">Submit</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('society_section')
    {{-- add + edit form validation and submit --}}
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <script>
        $(document).ready(function() {
            // disable modal outside click
            $('#addSocietyModal').modal({
                backdrop: 'static',
                keyboard: false
            });
            // check form1 validation and switch to next form2
            $(".modal").on('click', '.societyAddForm1', function(event) {
                event.preventDefault();
                var sname = $("#sname").val();
                var location = $("#location").val();
                var status = $("#statusSelect").val();
                var floors = $("#floors").val();
                var floorUnits = $("#floorUnits").val();
                var assignAdmin = $("#assignAdmin").val();

                if (sname == "" || !isNaN(sname)) {
                    $("#snameErr").text("Please enter a valid Name");
                    return false;
                }
                if (location == "" || !isNaN(location)) {
                    $("#locationErr").text("Please enter a valid Location");
                    return false;
                }
                if (status == "" || !isNaN(status)) {
                    $("#statusSelectErr").text("Please select from options");
                    return false;
                }
                if (floors == "" || isNaN(floors)) {
                    $("#floorsErr").text("Please enter a valid Number");
                    return false;
                }
                if (floorUnits == "" || isNaN(floorUnits)) {
                    $("#floorUnitsErr").text("Please enter a valid Number");
                    return false;
                }
                // if (assignAdmin == "" || isNaN(assignAdmin)) {
                //     $("#assignAdminErr").text("Please select from options");
                //     return false;
                // }

                let flag = validate_emergency_contacts();
                // alert(flag);
                if (flag) {
                    // toastr.error('Kindly complete all fields accurately !')
                    return false;
                }

                $('.add_society_wrapper .nav-link.active').parent().next().find(
                    '.nav-link').trigger(
                    'click');
            });

            // delete block in form2
            $(".block_wrapper").on('click', '.deleteBlock', function() {
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
                        $this.closest('.accordion-item').remove();
                        Swal.fire(
                            'Deleted!',
                            'The block has been removed.',
                            'success'
                        );
                        Swal.fire({
                            title: 'Deleted!',
                            text: 'The Block has been removed.',
                            icon: 'success',
                            showConfirmButton: false,
                            timer: 1500
                        });
                    }
                });
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
                            text: 'The Contact has been removed.',
                            icon: 'success',
                            showConfirmButton: false,
                            timer: 1500
                        });
                    }
                });
            });

            // check form2 validation and submit
            $("#societyAddForm2").click(function(event) {
                event.preventDefault();
                if ($('.accordion-item').length === 0) {
                    toastr.error('Please add atleast one Block !');
                    return 'returned';
                }
                let getFlag = validate_society_blocks('submit');
                if (getFlag) {
                    if (getFlag != 5) {

                        // toastr.error('Kindly complete all fields accurately !')
                    }
                    return false;
                }
                //direct submit for add and ajax submit for edit
                let formType = $('#societyAddForm2').data('formtype');
                if (formType == 'add') {
                    $('#societyAddForm').submit();
                } else {
                    //on modal-cancel relaod page to show fresh updated data
                    $('#societyAddForm').find('.cancel_btn').attr('onclick',
                        'window.location.reload()');
                    let formData = $('#societyAddForm').serialize();
                    $.ajax({
                        url: $('#societyAddForm').attr('action'),
                        method: 'POST',
                        data: formData,
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        },
                        success: function(response) {
                            toastr[response.status](response.message);
                            //add delay 2.5sec
                            setTimeout(function() {
                                window.location.reload();
                            }, 2500);
                        },
                        error: function(xhr, status, error) {
                            toastr[response.status](response.message);
                        }
                    });
                }
            });

            // show typed block name on accordion head
            $(document).on('keyup focus', 'input[name^="bname"]', function() {
                let bnameVal = $(this).val();
                let nameAttr = $(this).attr('name');
                let serial = nameAttr.match(/\[(\d+)\]/)[1];

                if (bnameVal.trim()) {
                    $(`#blockHeading${serial} .accordion-button`).find('.showBlockName').html('&nbsp;' +
                        bnameVal);
                }
            });
        });

        //validate
        function validate_emergency_contacts() {
            let setflag = 0;
            $('#emer_block .row').each(function() {
                let serial = $(this).data('sl');
                let hasError = false;
                console.log('serial', serial);

                // Validate Name
                let $nameInput = $(`input[name="emr_name[${serial}]"]`);
                let name = $nameInput.val();
                let $nameError = $nameInput.siblings('.err');
                let nameErrMsg = 'Name is required';
                let nameErrMsg_invalid = 'Invalid Entry';

                // setflag = 0;
                // hasError = 0;

                if (isEmpty(name, $nameError, nameErrMsg)) {
                    setflag = 1;
                    hasError = true;
                } else if (isInvalidName(name, $nameError, nameErrMsg_invalid)) {
                    setflag = 1;
                    hasError = true;
                }

                // Validate designation
                let $desigInput = $(`input[name="emr_designation[${serial}]"]`);
                let desig = $desigInput.val();
                let $desigError = $desigInput.siblings('.err');
                let desigErrMsg = 'Designation is required';
                let desigErrMsg_invalid = 'Invalid Entry';

                // setflag = 0;
                // hasError = 0;

                if (isEmpty(desig, $desigError, desigErrMsg)) {
                    setflag = 1;
                    hasError = true;
                } else if (isInvalidName(desig, $desigError, desigErrMsg_invalid)) {
                    setflag = 1;
                    hasError = true;
                }

                // validate phone number
                let $phoneInput = $(`input[name="emr_phone[${serial}]"]`);
                let phone = $phoneInput.val();
                let $phoneError = $phoneInput.siblings('.err');

                let phoneErrMsg = 'Phone is required';
                let phoneErrMsg_invalid = 'Must be 10 digits';

                // Initialize flags
                // setflag = 0;
                // hasError = 0;

                // Validate phone
                if (isEmpty(phone, $phoneError, phoneErrMsg)) {
                    setflag = 1;
                    hasError = true;
                } else if (isInvalidPhone(phone, $phoneError, phoneErrMsg_invalid)) {
                    setflag = 1;
                    hasError = true;
                }

            });

            return setflag;
        }

        function validate_society_blocks(whenEvent = null) {
            let setflag = 0;
            let allBlockTotalQty = 0;
            $('.accordion-item').each(function() {
                let serial = $(this).data('serial');
                let hasError = false;

                // Validate Block Name
                let bnameVal = $(`input[name="bname[${serial}]"]`).val();
                if (!bnameVal.trim()) {
                    $(`input[name="bname[${serial}]"]`).siblings('.err').text('Block Name is required.');
                    setflag = 1;
                    hasError = true;
                } else {
                    $(`input[name="bname[${serial}]"]`).siblings('.err').text('');
                }

                // Validate Total Units
                let totalUnitsVal = $(`input[name="totalUnits[${serial}]"]`).val();
                if (!totalUnitsVal.trim() || isNaN(totalUnitsVal)) {
                    $(`input[name="totalUnits[${serial}]"]`).siblings('.err').text(
                        'Total Units must be a number.');
                    setflag = 1;
                    hasError = true;
                } else {
                    $(`input[name="totalUnits[${serial}]"]`).siblings('.err').text('');
                }

                let totalQty = 0;
                $(this).find('tbody tr:not(:last)').each(function() {
                    // Validate Unit Size
                    let unit_sizeVal = $(this).find(`input[name="unit_size[${serial}][]"]`).val();
                    if (!unit_sizeVal.trim()) {
                        // $(this).find(`input[name="unit_size[${serial}][]"]`).siblings('.err').text(
                        //     'Unit Size is required.');
                        // setflag = 1;
                        // hasError = true;
                    } else {
                        $(this).find(`input[name="unit_size[${serial}][]"]`).siblings('.err').text(
                            '');
                    }

                    // Validate Unit Quantity
                    let unit_qtyVal = $(this).find(`input[name="unit_qty[${serial}][]"]`).val();
                    if (!unit_qtyVal.trim() || isNaN(unit_qtyVal)) {
                        // $(this).find(`input[name="unit_qty[${serial}][]"]`).siblings('.err').text(
                        //     'Quantity must be a number.');
                        // setflag = 1;
                        // hasError = true;
                        // $(this).find(`input[name="unit_qty[${serial}][]"]`).val(0);
                    } else {
                        // Accumulate total quantity after converting to number
                        totalQty += Number(unit_qtyVal);
                        $(this).find(`input[name="unit_qty[${serial}][]"]`).siblings('.err').text(
                            '');
                    }
                });

                // Compare Total Units with the sum of Unit Quantities
                if (!isNaN(totalUnitsVal) && !isNaN(totalQty) && Number(totalUnitsVal) !== totalQty) {
                    $(`input[name="totalUnits[${serial}]"]`).siblings('.err').text(
                        'Not Matched with Below Units Qty');
                    setflag = 1;
                    hasError = true;
                }

                allBlockTotalQty += Number(totalQty);

                // Open the accordion section if there are any errors
                let collapseId = `#blockCollapse${serial}`;
                if (hasError) {
                    $(collapseId).addClass('show');
                    $(`#blockHeading${serial} .accordion-button`).removeClass('collapsed');
                    $(`#blockHeading${serial} .accordion-button`).attr('aria-expanded', true);
                } else {
                    $(collapseId).removeClass('show');
                    $(`#blockHeading${serial} .accordion-button`).addClass('collapsed');
                    $(`#blockHeading${serial} .accordion-button`).attr('aria-expanded', false);
                }
            });

            if (whenEvent != null) {
                let floors = Number($("#floors").val());
                let floorUnits = Number($("#floorUnits").val());
                // Validate Total Quantity
                let totalSocietyUnits = floors * floorUnits;
                if (!isNaN(totalSocietyUnits) && !isNaN(allBlockTotalQty) && Number(totalSocietyUnits) !==
                    allBlockTotalQty) {
                    toastr.error('PLease ensure society total unit and all blocks total unit are matched')
                    setflag = 5;
                    hasError = true;
                }
            }

            return setflag;
        }
    </script>
    {{-- show edit form --}}
    <script>
        $(document).ready(function() {
            $('#addSocietyModalOpenBtn').on('click', function() {
                $('#societyAddForm').attr('action',
                    '{{ route($thisModule . '.society.store') }}'
                );
                $('#submitAddMemberForm').attr('data-formtype', 'add');
                $('#modalHeadTxt').text('Add Society');

                // let csrfToken = $('#societyAddForm input[name="_token"]').val();

                // $('#societyAddForm')[0].reset();
                // $('#societyAddForm input, #societyAddForm select, #societyAddForm textarea').val('');
                // Reset the form, but exclude specific fields by name
                $('#societyAddForm').find(
                    'input:not([name="_token"],[name^="unit_type"]), select, textarea').each(
                    function() {
                        $(this).val('');
                    });

                $('#societyAddForm select').each(function() {
                    $(this).prop('selectedIndex', 0); // Select the first option
                });
                // $('#societyAddForm input[name="_token"]').val(csrfToken);
            });

            $('.edit').on('click', function() {
                // loader
                $('#loader').css('width', '50%');
                $('#loader').fadeIn();
                $('#blockOverlay').fadeIn();

                // $('#societyAddForm')[0].reset();
                // $('#societyAddForm input, #societyAddForm select, #societyAddForm textarea').val('');
                $('#societyAddForm').find(
                    'input:not([name="_token"],[name^="unit_type"]), select, textarea').each(
                    function() {
                        $(this).val('');
                    });

                $('#societyAddForm select').each(function() {
                    $(this).prop('selectedIndex', 0); // Select the first option
                });
                $('.text-danger').text('');
                // change modal heading
                $('#modalHeadTxt').text('Edit Society');

                const societyId = $(this).attr('id');

                $.ajax({
                    url: "{{ route($thisModule . '.society.edit', ['id' => ':societyId']) }}"
                        .replace(':societyId', societyId),
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        let data = res.data;
                        if (res.status == 'error') {
                            toastr[res.status](res.message);
                        }
                        $('#societyAddForm').attr('action',
                            '{{ route($thisModule . '.society.update', ['id' => '__ID__']) }}'
                            .replace('__ID__', data.id));
                        $('#societyAddForm2').attr('data-formtype', 'edit');

                        // modal data
                        const assignAdminSelect = $('#assignAdmin');
                        assignAdminSelect.empty();
                        assignAdminSelect.append(
                            '<option value="" selected>--select--</option>');
                        console.log(res.admins);

                        // Check if response.admins is not empty and an array
                        if (Array.isArray(res.admins) && res.admins.length > 0) {
                            // Loop through the admins and create new options
                            res.admins.forEach(function(admin) {
                                assignAdminSelect.append(
                                    `<option value="${admin.id}">${admin.name}</option>`
                                );
                            });
                        } else {
                            // Optionally, add a message if there are no admins
                            assignAdminSelect.append(
                                '<option value="" disabled>No admins available</option>');
                        }

                        $('#sname').val(data.name);
                        $('#location').val(data.location);
                        $('#statusSelect').val(data.status);

                        $('#floors').val(data.floors);
                        $('#floorUnits').val(data.floor_units);
                        $('#assignAdmin').val(data.member_id);
                        $('#accordionBlock').empty();
                        $('#emer_block').empty();
                        //Group by emergency contacts name
                        var groupedContacts = groupBy(data.society_contacts, 'phone');
                        $.each(groupedContacts, function(phoneNumber, contactData) {
                            var contactIndex = Object.keys(groupedContacts).indexOf(
                                phoneNumber) + 1;
                            var emrName = contactData[0].name;
                            var emrDesig = contactData[0].designation;
                            if (contactIndex == 1) {
                                var addBtn = `<a href="javascript:void(0)" id="add_emer_row"
                                                class="btn btn-primary mt-4">+
                                            </a>`;
                            } else {
                                var addBtn = ` <a href="javascript:void(0)"
                                                class="btn btn-danger remove_emer_row mt-4">-
                                            </a>`;
                            }
                            var emrRowHtml = `<div class="row" data-sl="${contactIndex}">
                                                <div class="col">
                                                    <div class="form-group">
                                                        <label>Name</label>
                                                        <input type="text" name="emr_name[${contactIndex}]" value="${emrName}"
                                                            class="form-control">
                                                        <span class="text-danger err"></span>
                                                    </div>
                                                </div>
                                                <div class="col">
                                                    <div class="form-group">
                                                        <label>Designation</label>
                                                        <input type="text" name="emr_designation[${contactIndex}]" value="${emrDesig}"
                                                            class="form-control">
                                                        <span class="text-danger err"></span>
                                                    </div>
                                                </div>
                                                <div class="col">
                                                    <div class="form-group">
                                                        <label>Phone</label>
                                                        <input type="text" name="emr_phone[${contactIndex}]" value="${phoneNumber}"
                                                            class="form-control">
                                                        <span class="text-danger err"></span>
                                                    </div>
                                                </div>
                                                <div class="col">
                                                    <div class="form-group">
                                                        ${addBtn}
                                                    </div>
                                                </div>
                                            </div>`;
                            $('#emer_block').append(emrRowHtml);
                        });
                        // Group blocks by name
                        var groupedBlocks = groupBy(data.blocks, 'name');
                        // Iterate over the grouped blocks
                        $.each(groupedBlocks, function(blockName, blocks) {
                            var blockIndex = Object.keys(groupedBlocks).indexOf(
                                blockName) + 1;
                            //same blocks-same total_units
                            var totalUnits = blocks[0].total_units;
                            // Create the accordion item for each unique block name
                            var accordionItem = `
                                    <div class="accordion-item" data-serial="${blockIndex}">
                                        <h2 class="accordion-header" id="blockHeading${blockIndex}">
                                            <button class="accordion-button" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#blockCollapse${blockIndex}"
                                                aria-expanded="true" aria-controls="blockCollapse${blockIndex}">
                                                 <span class="showBlockName">${blockName}</span>
                                            </button>
                                        </h2>
                                        <div id="blockCollapse${blockIndex}" class="accordion-collapse collapse show"
                                            aria-labelledby="blockHeading${blockIndex}" data-bs-parent="#accordionBlock">
                                            <div class="accordion-body">
                                                <div class="block_fields">
                                                    <div class="custom_form">
                                                        <div class="form">
                                                            <div class="row">
                                                                <div class="col">
                                                                    <div class="form-group">
                                                                        <label>Block Name</label>
                                                                        <input type="text" name="bname[${blockIndex}]"
                                                                            class="form-control" value="${blockName}">

                                                                        <span class="text-danger err"></span>
                                                                    </div>
                                                                </div>
                                                                <div class="col">
                                                                    <div class="form-group">
                                                                        <label>Total Units</label>
                                                                        <input type="text" name="totalUnits[${blockIndex}]"
                                                                            class="form-control" value="${totalUnits}">

                                                                        <span class="text-danger err"></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="blocks_table">
                                                        <h5>Units</h5>
                                                        <div class="table-responsive">
                                                            <table width="100%" cellpadding="0" cellspacing="0">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Unit Type</th>
                                                                        <th>Size (sq.ft) </th>
                                                                        <th>QTY</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    ${generateUnitRows(blocks, blockIndex)}
                                                                    <tr>
                                                                        <td colspan="3">
                                                                            <button type="button" class="deleteBlock btn btn-danger mt-2"
                                                                                style="width:100%">Delete</button>
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                `;
                            $('#accordionBlock').append(accordionItem);
                        });

                        //group blocks by name
                        function groupBy(array, key) {
                            return array.reduce(function(result, currentValue) {
                                // Check if the key exists already
                                (result[currentValue[key]] = result[currentValue[
                                    key]] || []).push(currentValue);
                                return result;
                            }, {});
                        }

                        function generateUnitRows(blocks, blockIndex) {
                            var rows = '';

                            $.each(blocks, function(i, block) {
                                rows += `
                                    <tr>
                                        <td>
                                            ${block.unit_type.charAt(0).toUpperCase() + block.unit_type.slice(1)}
                                            <input type="hidden" name="unit_type[${blockIndex}][]" value="${block.unit_type}">
                                            <input type="hidden" name="block_id[${blockIndex}][]" value="${block.id}">
                                            <br>
                                            <span class="text-danger err"></span>
                                        </td>
                                        <td>
                                            <input type="text" name="unit_size[${blockIndex}][]" class="block-field" value="${block.unit_size}">
                                            <br>
                                            <span class="text-danger err"></span>
                                        </td>
                                        <td>
                                            <input type="number" name="unit_qty[${blockIndex}][]" class="block-field" value="${block.unit_qty}">
                                            <br>
                                            <span class="text-danger err"></span>
                                        </td>
                                    </tr>
                                `;
                            });
                            return rows;
                        }

                        $('#addSocietyModal').modal('show');
                        //loader removed
                        $('#loader').css('width', '100%');
                        $('#loader').fadeOut();
                        $('#blockOverlay').fadeOut();
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
                var societyId = $(this).data('id');
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
                            url: "{{ route($thisModule . '.society.delete', ['id' => ':societyId']) }}"
                                .replace(':societyId', societyId),
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


            var societyId = $(this).data('id');
            Swal.fire({
                title: 'Are you sure?',
                text: "You want to change the status",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, change it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route($thisModule . '.society.status.change', ['id' => ':societyId', 'status' => ':toStatus']) }}"
                            .replace(':societyId', societyId)
                            .replace(':toStatus', toStatus),
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
