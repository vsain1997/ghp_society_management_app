<style>
    .custom-table>:not(caption)>*>* {
    border-bottom-width: 0 !important;
  }
</style>
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
                        <select name="society" class="form-control">
                            <option value="none" disabled>--Choose--</option>
                            <option value="published" {{ request('society') == 'published' ? 'selected' : '' }}>Published</option>
                            <option value="draft" {{ request('society') == 'draft' ? 'selected' : '' }}>Draft
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
                        <a href="{{ route($thisModule . '.settings') }}" class="resetbtn" style="font-size: 18px; background: #4b40b5; color: white; padding: 9px 15px; border-radius: 6px; margin-left: 7px;">Reset</a>

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
                    <th class="text-center">Street Address </th>
                    <th class="text-center">Total Properties</th>
                    <th class="text-center d-none">Status</th>
                    <th class="text-center">Actions</th>
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
                            <td class="text-center">{{ $society->location }}</td>
                            <td class="text-center">{{ count($society->blocks) }}</td>
                            {{-- <td>{{ $society->total_society_units }}</td> --}}
                            <td class="text-center d-none">
                                <input type="hidden" name="statusVal" value="{{ parseStatus($society->status, 0) }}">
                                <div class="status_select">
                                    <select name="status" data-id="{{ $society->id }}"
                                        class="statusOption form-select">
                                        <option value="active" {{ parseStatus($society->status, 1) }}>Active
                                        </option>
                                        <option value="inactive" {{ parseStatus($society->status, 2) }}>Inactive</option>
                                    </select>
                                </div>
                            </td>
                            <td class="text-center">
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
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
                                                    <label for="location">Street Address</label>
                                                    <input type="text" name="location" id="location"
                                                        class="form-control">
                                                    <span id="locationErr" class="text-danger"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col">
                                                <div class="form-group">
                                                    <label for="city">City</label>
                                                    <input type="text" name="city" id="city"
                                                        class="form-control">
                                                    <span id="cityErr" class="text-danger"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col">
                                                <div class="form-group">
                                                    <label for="state">State/UT</label>
                                                    <select name="state" id="state"
                                                        class="form-select form-control ">
                                                        @foreach($stateList as $state)
                                                            <option value="{{ $state }}">{{ $state }}</option>
                                                        @endforeach
                                                    </select>
                                                    <span id="stateErr" class="text-danger"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col">
                                                <div class="form-group">
                                                    <label for="pin">Pin Code</label>
                                                    <input type="text" name="pin" id="pin"
                                                        class="form-control">
                                                    <span id="pinErr" class="text-danger"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col">
                                                <div class="form-group">
                                                    <label for="societyContact">Society Contact Number</label>
                                                    <input type="text" name="societyContact" id="societyContact"
                                                        class="form-control phone-input-restrict">
                                                    <span id="societyContactErr" class="text-danger"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col">
                                                <div class="form-group">
                                                    <label for="societyEmail">Society Email</label>
                                                    <input type="text" name="societyEmail" id="societyEmail"
                                                        class="form-control email-input-restrict">
                                                    <span id="societyEmailErr" class="text-danger"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col">
                                                <div class="form-group">
                                                    <label for="reg">Registration Number</label>
                                                    <input type="text" name="reg" id="reg"
                                                        class="form-control">
                                                    <span id="regErr" class="text-danger"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col">
                                                <div class="form-group">
                                                    <label for="societyTypeSelect">Society Type</label>
                                                    <select name="societyTypeSelect" id="societyTypeSelect"
                                                        class="form-select form-control ">
                                                        <option value="residential" selected>Residential</option>
                                                        <option value="commercial" >Commercial</option>
                                                        <option value="mixed">Mixed</option>
                                                    </select>
                                                    <span id="societyTypeSelectErr" class="text-danger"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row d-none">
                                            <div class="col">
                                                <div class="form-group">
                                                    <label for="statusSelect">Status</label>
                                                    <select name="statusSelect" id="statusSelect"
                                                        class="form-select form-control ">
                                                        <option value="active" selected>Active</option>
                                                        <option value="inactive">Inactive</option>
                                                    </select>
                                                    <span id="statusSelectErr" class="text-danger"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col">
                                                <div class="form-group">
                                                    <label for="societyArea">Total Area (Sq.Yard)</label>
                                                    <input type="text" name="societyArea" id="societyArea"
                                                        class="form-control">
                                                    <span id="societyAreaErr" class="text-danger"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col">
                                                <div class="form-group">
                                                    <label for="totalTowers">Total Number Of Towers/Total Number Of Blocks</label>
                                                    <input type="text" name="totalTowers" id="totalTowers"
                                                        class="form-control">
                                                    <span id="totalTowersErr" class="text-danger"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <p>Amenities</p>
                                        {{-- <div class="row">
                                            <div class="col form-check">
                                                <input class="form-check-input" type="checkbox"
                                                     value="Swimming Pool" id="amn1" style="height:20px;width:20px;border-radius:0 !important">
                                                <label class="form-check-label" for="amn1">
                                                    Swimming Pool
                                                </label>
                                            </div>
                                            <div class="col form-check">
                                                <input class="form-check-input" type="checkbox"
                                                     value="Gym" id="amn2" style="height:20px;width:20px;border-radius:0 !important">
                                                <label class="form-check-label" for="amn2">
                                                    Gym
                                                </label>
                                            </div>
                                        </div> --}}
                                        <div class="d-inline-flex flex-wrap gap-3">

                                            <table class="table custom-table table-responsive">
                                                @foreach (array_chunk($amenities, 5) as $amenityRow) {{-- Group amenities into rows of 4 --}}
                                                    <tr>
                                                        @foreach ($amenityRow as $amenity)
                                                            <td>
                                                                <div class="form-check d-inline-flex">
                                                                    <input type="checkbox"
                                                                    name="amenities[]" value="{{ $amenity['name'] }}" class="form-check-input" data-amenity="{{ $amenity['name'] }}" style="height:20px;width:20px;border-radius:0 !important">
                                                                    <label class="p-1 form-check-label" for="{{ $amenity['name'] }}">
                                                                        {{ $amenity['name'] }}
                                                                    </label>
                                                                </div>
                                                            </td>
                                                        @endforeach

                                                        {{-- Fill empty cells if the row has less than 4 columns --}}
                                                        @for ($i = count($amenityRow); $i < 5; $i++)
                                                            <td></td>
                                                        @endfor
                                                    </tr>
                                                @endforeach
                                            </table>

                                            {{-- <div class="form-check d-inline-flex">
                                              <input class="form-check-input" type="checkbox" value="Gym" id="amn2" style="height:20px;width:20px;border-radius:0 !important">
                                              <label class="p-1 form-check-label" for="amn2">
                                                Gym
                                              </label>
                                            </div> --}}
                                          </div>

                                        <div class="row d-none">
                                            <div class="col">
                                                <div class="form-group">
                                                    <label for="floors">Number Of Floors <svg fill="#636363" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="10px" height="10px" viewBox="0 0 416.979 416.979" xml:space="preserve" stroke="#636363"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <g> <path d="M356.004,61.156c-81.37-81.47-213.377-81.551-294.848-0.182c-81.47,81.371-81.552,213.379-0.181,294.85 c81.369,81.47,213.378,81.551,294.849,0.181C437.293,274.636,437.375,142.626,356.004,61.156z M237.6,340.786 c0,3.217-2.607,5.822-5.822,5.822h-46.576c-3.215,0-5.822-2.605-5.822-5.822V167.885c0-3.217,2.607-5.822,5.822-5.822h46.576 c3.215,0,5.822,2.604,5.822,5.822V340.786z M208.49,137.901c-18.618,0-33.766-15.146-33.766-33.765 c0-18.617,15.147-33.766,33.766-33.766c18.619,0,33.766,15.148,33.766,33.766C242.256,122.755,227.107,137.901,208.49,137.901z"></path> </g> </g></svg></label>
                                                    <input type="text" name="floors" id="floors"
                                                        class="form-control">
                                                    <span id="floorsErr" class="text-danger"></span>
                                                </div>
                                            </div>
                                            <div class="col d-none">
                                                <div class="form-group">
                                                    <label for="floorUnits">Units Per Floor <svg fill="#636363" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="10px" height="10px" viewBox="0 0 416.979 416.979" xml:space="preserve" stroke="#636363"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <g> <path d="M356.004,61.156c-81.37-81.47-213.377-81.551-294.848-0.182c-81.47,81.371-81.552,213.379-0.181,294.85 c81.369,81.47,213.378,81.551,294.849,0.181C437.293,274.636,437.375,142.626,356.004,61.156z M237.6,340.786 c0,3.217-2.607,5.822-5.822,5.822h-46.576c-3.215,0-5.822-2.605-5.822-5.822V167.885c0-3.217,2.607-5.822,5.822-5.822h46.576 c3.215,0,5.822,2.604,5.822,5.822V340.786z M208.49,137.901c-18.618,0-33.766-15.146-33.766-33.765 c0-18.617,15.147-33.766,33.766-33.766c18.619,0,33.766,15.148,33.766,33.766C242.256,122.755,227.107,137.901,208.49,137.901z"></path> </g> </g></svg></label>
                                                    <input type="text" name="floorUnits" id="floorUnits"
                                                        class="form-control">
                                                    <span id="floorUnitsErr" class="text-danger"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row d-none">
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
                                                            class="form-control phone-input-restrict">
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
                               <div class="memberBx">
                                    <form action="{{ route($thisModule . '.member.import') }}" method="POST" enctype="multipart/form-data" >
                                        @csrf
                                        <div class="choosefile flex">
                                            <div class="">
                                                <!-- <label for="fileInput" class="form-label">Choose File</label> -->
                                                <input class="form-control" type="file" id="fileInput" name="importedFile" accept=".csv, .xlsx, .xls, .txt" required>
                                                <input type="hidden" name="society_id" id="society_id" value="{{ session('__selected_society__') }}">
                                            </div>
                                            <button type="submit" class="btn btn-success">Upload</button>
                                        </div>
                                    </form>                
                                </div>
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
                                                                            <label>Tower Name/Block Name</label>
                                                                            <input type="text" name="bname[1]"
                                                                                class="form-control">

                                                                            <span class="text-danger err"></span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col">
                                                                        <div class="form-group">
                                                                            <label>Total Units</label>
                                                                            <input type="text" name="totalFloors[1]"
                                                                                class="form-control">

                                                                            <span class="text-danger err"></span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="blocks_table">
                                                            {{-- <h5>Property Information</h5> --}}
                                                            <div class="table-responsive">
                                                                <table width="100%" cellpadding="0"
                                                                    cellspacing="0">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Property Number</th>
                                                                            <th>Floor/Unit No.</th>
                                                                            <th>Property Type</th>
                                                                            <th class="d-none">Ownership</th>
                                                                            <th>Size (Sq.Yard) </th>
                                                                            <th>BHK</th>
                                                                            <th>&nbsp; </th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody id="tower_property_1">
                                                                        <tr>
                                                                            <td>
                                                                                <input type="hidden" name="block_id[1][]">
                                                                                <input type="text"
                                                                                    name="property_number[1][]" class="block-field" style="width: 100%;">
                                                                                <br>
                                                                                <span class="text-danger err"></span>
                                                                            </td>
                                                                            <td>
                                                                                <input type="text"
                                                                                    name="property_floor[1][]"
                                                                                    class="block-field">
                                                                                <br>
                                                                                <span class="text-danger err"></span>
                                                                            </td>
                                                                            <td>
                                                                                <select name="property_type[1][]" class="block-field" id="">
                                                                                    <option value="residential">Residential</option>
                                                                                    <option value="commercial">Commercial</option>
                                                                                </select>
                                                                                <br>
                                                                                <span class="text-danger err"></span>
                                                                            </td>
                                                                            <td class="d-none">
                                                                                <select name="ownership[1][]" class="block-field" id="">
                                                                                    <option value="vacant">Vacant</option>
                                                                                    <option value="occupied">Occupied</option>
                                                                                </select>
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
                                                                                <select name="bhk[1][]" class="block-field" id="">
                                                                                    <option value="1">1 BHK</option>
                                                                                    <option value="2">2 BHK</option>
                                                                                    <option value="3">3 BHK</option>
                                                                                    <option value="4">4 BHK</option>
                                                                                    <option value="5">5 BHK</option>
                                                                                </select>
                                                                                <br>
                                                                                <span class="text-danger err"></span>
                                                                            </td>
                                                                            <td>
                                                                                <a href="javascript:void(0)" data-tower-number="1"
                                                                                    class="btn btn-primary btn-sm add_property_row">
                                                                                    +
                                                                                </a>
                                                                            </td>

                                                                        </tr>

                                                                    </tbody>
                                                                    <tfoot>
                                                                        <tr>
                                                                            <td colspan="7">
                                                                                <button type="button"
                                                                                class="deleteBlock btn btn-danger mt-2"
                                                                                style="width:100%">Delete</button>
                                                                            </td>
                                                                        </tr>
                                                                    </tfoot>
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
                                                <button type="button" data-formtype="draftAdd" id="societyAddFormDraft"
                                                    class="bg_theme_btn">Save As Draft</button>
                                                <button type="button" data-formtype="add" id="societyAddForm2"
                                                    class="bg_theme_btn">Save And Publish</button>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>

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
                // $('.add_society_wrapper .nav-link.active').parent().next().find(
                //     '.nav-link').trigger(
                //     'click');
                var sname = $("#sname").val();
                var location = $("#location").val();
                var city = $("#city").val();
                var state = $("#state").val();
                var pin = $("#pin").val();
                var status = $("#statusSelect").val();
                var societyContact = $("#societyContact").val();
                var societyEmail = $("#societyEmail").val();
                var societyArea = $("#societyArea").val();
                var totalTowers = $("#totalTowers").val();
                // var floors = $("#floors").val();
                // var floorUnits = $("#floorUnits").val();
                // var assignAdmin = $("#assignAdmin").val();

                if (sname == "" || !isNaN(sname)) {
                    $("#snameErr").text("Please enter a valid Name");
                    return false;
                }else{
                    $("#snameErr").text("");
                }
                if (location == "" || !isNaN(location)) {
                    $("#locationErr").text("Please enter a valid Address");
                    return false;
                }else{
                    $("#locationErr").text("");
                }
                if (city == "" || !isNaN(city)) {
                    $("#cityErr").text("Please enter a valid City");
                    return false;
                }else{
                    $("#cityErr").text("");
                }
                if (state == "" || !isNaN(state)) {
                    $("#stateErr").text("Please select a valid option");
                    return false;
                }else{
                    $("#stateErr").text("");
                }
                if (pin == "" || isNaN(pin)) {
                    $("#pinErr").text("Please enter a valid Pin");
                    return false;
                }else{
                    $("#pinErr").text("");
                }
                if (status == "" || !isNaN(status)) {
                    $("#statusSelectErr").text("Please select from options");
                    return false;
                }else{
                    $("#statusSelectErr").text("");
                }
                if (societyArea == "" || isNaN(societyArea)) {
                    $("#societyAreaErr").text("Please enter a valid Number");
                    return false;
                }else{
                    $("#societyAreaErr").text("");
                }
                if (totalTowers == "" || isNaN(totalTowers)) {
                    $("#totalTowersErr").text("Please enter a valid Number");
                    return false;
                }else{
                    $("#totalTowersErr").text("");
                }

                // if (floorUnits == "" || isNaN(floorUnits)) {
                //     $("#floorUnitsErr").text("Please enter a valid Number");
                //     return false;
                // }
                // if (assignAdmin == "" || isNaN(assignAdmin)) {
                //     $("#assignAdminErr").text("Please select from options");
                //     return false;
                // }

                // validate phone number
                let $phoneError = $("#societyContactErr");
                let phoneErrMsg_invalid = 'Please enter a valid phone number';
                if (isNotEmpty(societyContact) && isInvalidPhone(societyContact, $phoneError, phoneErrMsg_invalid)) {
                    return false;
                }

                // validate email
                let $emailError = $("#societyEmailErr");
                let emailErrMsg_invalid = 'Please enter a valid email address';
                if (isNotEmpty(societyEmail) && isInvalidEmail(societyEmail, $emailError, emailErrMsg_invalid)) {
                    return false;
                }

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
            $(document).on('click', '.remove_property_row', function() {
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
                        $this.closest('tr').remove();
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

            // check form2 validation and submit
            $("#societyAddForm2").click(function(event) {
                event.preventDefault();
                // if ($('.accordion-item').length === 0) {
                //     toastr.error('Please add atleast one Block !');
                //     return 'returned';
                // }
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
                    $('#statusSelect').val('active').change();
                    //on modal-cancel relaod page to show fresh updated data
                    $('#societyAddForm').find('.cancel_btn').attr('onclick',
                        'window.location.reload()');
                    let formData = $('#societyAddForm').serialize();
                    $.ajax({
                        url: $('#societyAddForm').attr('action'),
                        method: 'POST',
                        data: formData,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
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

            // check form2 validation and submit
            $("#societyAddFormDraft").click(function(event) {
                event.preventDefault();
                $('#statusSelect').val('inactive').change();
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

        function validate_unique_blocks() {
            let blockNames = [];
            let hasDuplicates = 0;

            // Loop through each accordion item to collect and validate block names
            $('.accordion-item').each(function() {
                let serial = $(this).data('serial');
                let bnameInput = $(`input[name="bname[${serial}]"]`);

                let bnameVal = bnameInput.val().trim();
                console.log('block', serial, bnameVal);

                // Reset error message
                bnameInput.siblings('.err').text('');

                if (blockNames.includes(bnameVal)) {
                    // Mark duplicate block names as invalid
                    bnameInput.siblings('.err').text('Tower Name should be unique.');
                    hasDuplicates = 1;
                } else {
                    // Add valid block name to the list
                    blockNames.push(bnameVal);
                }
            });

            if (hasDuplicates != 1) {
                return 0;
            } else {
                toastr.error('Tower Name should be unique')
                return 5; //restrict another message
            }

            // Return a boolean indicating whether there are duplicates
        }

        function validateFloorNumbers(total_floors_per_society) {
            let checkFlag = 0;
            $('input[name^="floorNumber"]').each(function() {
                let typedfloorNumber = parseInt($(this).val()) || 0; // Parse input value to integer
                let errorSpan = $(this).siblings('.err'); // Target the error span

                // Check if floor number is invalid
                if (typedfloorNumber === 0 || typedfloorNumber > total_floors_per_society) {
                    errorSpan.text('Invalid Floor Number'); // Show error
                    checkFlag = 1;
                } else {
                    errorSpan.text(''); // Clear error if valid
                }
            });

            if (checkFlag != 1) {
                return 0;
            } else {
                toastr.error('Invalid Floor Number !')
                return 5; //restrict another message
            }
        }

        function validateTotalUnitsPerFloor(total_unit_on_each_floor) {
            let chkFlag = 0;
            let floorUnitMap = {}; // To store the total units for each floor

            // Loop through all the floor numbers and their corresponding total units
            $('input[name^="floorNumber"]').each(function() {
                let floorNumber = parseInt($(this).val()) || 0; // Get the floor number
                let totalUnitsInput = $(this)
                    .closest('.row')
                    .find('input[name^="totalUnits"]'); // Find corresponding total units input
                let totalUnits = parseInt(totalUnitsInput.val()) || 0; // Get the total units

                // Skip if the floor number is invalid
                if (floorNumber <= 0) return;

                // Accumulate total units for this floor
                if (!floorUnitMap[floorNumber]) {
                    floorUnitMap[floorNumber] = 0; // Initialize if not present
                }
                floorUnitMap[floorNumber] += totalUnits;

                // Check if the total units exceed the limit
                if (floorUnitMap[floorNumber] > total_unit_on_each_floor) {
                    totalUnitsInput
                        .siblings('.err')
                        .text('Capacity exceeds, max floor units is ' + total_unit_on_each_floor); // Show error
                    chkFlag = 1;
                } else {
                    totalUnitsInput.siblings('.err').text(''); // Clear error if valid
                }
            });

            if (chkFlag != 1) {
                return 0;
            } else {
                toastr.error('Exceeds max floor units !')
                return 5; //restrict another message
            }
        }


        function validate_society_blocks(whenEvent = null) {
            let setflag = 0;
            $('.accordion-item').each(function() {
                let serial = $(this).data('serial');
                let hasError = false;
                let societyType = $(`select[name="societyTypeSelect"]`).val();

                // Validate Block Name
                let bnameVal = $(`input[name="bname[${serial}]"]`).val();
                if (!bnameVal.trim()) {

                    $(`input[name="bname[${serial}]"]`).siblings('.err').text('Required');
                    setflag = 1;
                    hasError = true;
                } else {
                    $(`input[name="bname[${serial}]"]`).siblings('.err').text('');
                }

                // Validate Total Units
                let totalFloorsVal = $(`input[name="totalFloors[${serial}]"]`).val();
                if (!totalFloorsVal.trim()) {
                    $(`input[name="totalFloors[${serial}]"]`).siblings('.err').text(
                        'Required');
                    setflag = 1;
                    hasError = true;
                } else if(isNaN(totalFloorsVal) || totalFloorsVal == 0){
                    $(`input[name="totalFloors[${serial}]"]`).siblings('.err').text(
                        'Invalid');
                    setflag = 1;
                    hasError = true;
                }
                else {
                    $(`input[name="totalFloors[${serial}]"]`).siblings('.err').text('');
                }

                let totalQty = 0;
                $(this).find('tbody tr').each(function() {
                    // Validate property number
                    let property_numberVal = $(this).find(`input[name="property_number[${serial}][]"]`).val();
                    if (!property_numberVal.trim()) {
                        $(this).find(`input[name="property_number[${serial}][]"]`).siblings('.err').text(
                            'Required');
                        setflag = 1;
                        hasError = true;
                    } else if(property_numberVal == 0){
                        $(this).find(`input[name="property_number[${serial}][]"]`).siblings('.err').text(
                            'Invalid');
                        setflag = 1;
                        hasError = true;
                    } else {
                        $(this).find(`input[name="property_number[${serial}][]"]`).siblings('.err').text(
                            '');
                    }

                    // Validate floor
                    let property_floorVal = $(this).find(`input[name="property_floor[${serial}][]"]`).val();
                    if (!property_floorVal.trim()) {
                        $(this).find(`input[name="property_floor[${serial}][]"]`).siblings('.err').text(
                            'Required');
                        setflag = 1;
                        hasError = true;
                    } else if(!isNaN(property_floorVal) && property_floorVal > Number(totalFloorsVal) ){
                        $(this).find(`input[name="property_floor[${serial}][]"]`).siblings('.err').text(
                            'Invalid');
                        setflag = 1;
                        hasError = true;
                    } else {
                        $(this).find(`input[name="property_floor[${serial}][]"]`).siblings('.err').text(
                            '');
                    }

                    // Validate size
                    let unit_sizeVal = $(this).find(`input[name="unit_size[${serial}][]"]`).val();
                    if (!unit_sizeVal.trim()) {
                        $(this).find(`input[name="unit_size[${serial}][]"]`).siblings('.err').text(
                            'Required');
                        setflag = 1;
                        hasError = true;
                    } else if(isNaN(unit_sizeVal) || unit_sizeVal == 0){
                        $(this).find(`input[name="unit_size[${serial}][]"]`).siblings('.err').text(
                            'Invalid');
                        setflag = 1;
                        hasError = true;
                    } else {
                        $(this).find(`input[name="unit_size[${serial}][]"]`).siblings('.err').text(
                            '');
                    }

                    //check property type
                    let property_type = $(this).find(`select[name="property_type[${serial}][]"]`).val();

                    // console.log(property_type ,societyType);

                    if(societyType != 'mixed'){

                        if(societyType != property_type){
                            $(this).find(`select[name="property_type[${serial}][]"]`).siblings('.err').text(
                                'Invalid');
                            setflag = 1;
                            hasError = true;
                        }else{
                            $(this).find(`select[name="property_type[${serial}][]"]`).siblings('.err').text(
                                '');
                        }
                    }

                });

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

            if (whenEvent != null && setflag != 1) {

                if (validate_unique_blocks() == 5) {
                    setflag = 5;
                }
                // if (validateFloorNumbers(floors) == 5) {
                //     setflag = 5;
                // }
                // if (validateTotalUnitsPerFloor(total_unit_on_each_floor) == 5) {
                //     setflag = 5;
                // }
            }

            return setflag;
        }
    </script>
    {{-- show edit form --}}
    <script>
        $(document).ready(function() {
            $('#addSocietyModalOpenBtn').on('click', function() {

                // $('.add_society_wrapper .nav-link.active').parent().next().find(
                //     '.nav-link').trigger(
                //     'click');

                $('#societyAddForm').attr('action',
                    '{{ route($thisModule . '.society.store') }}'
                );
                $('#submitAddMemberForm').attr('data-formtype', 'add');
                $('#modalHeadTxt').text('Add Society');
                $('a.remove_property_row').closest('tr').remove();
                // add btn for draft and publish
                $('#societyAddFormDraft').show();
                $('#societyAddFormDraft').text('Save As Draft');
                $('#societyAddForm2').text('Save And Publish');

                $('#accordionBlock').empty();
                // Append the new HTML
                $('#accordionBlock').append(`
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
                                                       <label>Tower Name/Block Name</label>
                                                        <input type="text" name="bname[1]" class="form-control">
                                                        <span class="text-danger err"></span>
                                                    </div>
                                                </div>
                                                <div class="col">
                                                    <div class="form-group">
                                                        <label>Total Units</label>
                                                        <input type="text" name="totalFloors[1]" class="form-control">
                                                        <span class="text-danger err"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="blocks_table">
                                        <div class="table-responsive">
                                            <table width="100%" cellpadding="0" cellspacing="0">
                                                <thead>
                                                    <tr>
                                                        <th>Property Number</th>
                                                        <th>Floor/Unit No.</th>
                                                        <th>Property Type</th>
                                                        <th class="d-none">Ownership</th>
                                                        <th>Size (Sq.Yard)</th>
                                                        <th>BHK</th>
                                                        <th>&nbsp;</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="tower_property_1">
                                                    <tr>
                                                        <td>
                                                            <input type="hidden" name="block_id[1][]">
                                                            <input type="text" name="property_number[1][]" class="block-field" style="width: 100%;">
                                                            <br>
                                                            <span class="text-danger err"></span>
                                                        </td>
                                                        <td>
                                                            <input type="text" name="property_floor[1][]" class="block-field">
                                                            <br>
                                                            <span class="text-danger err"></span>
                                                        </td>
                                                        <td>
                                                            <select name="property_type[1][]" class="block-field">
                                                                <option value="commercial">Commercial</option>
                                                                <option value="residential">Residential</option>
                                                            </select>
                                                            <br>
                                                            <span class="text-danger err"></span>
                                                        </td>
                                                        <td class="d-none">
                                                            <select name="ownership[1][]" class="block-field">
                                                                <option value="vacant">Vacant</option>
                                                                <option value="occupied">Occupied</option>
                                                            </select>
                                                            <br>
                                                            <span class="text-danger err"></span>
                                                        </td>
                                                        <td>
                                                            <input type="text" name="unit_size[1][]" class="block-field">
                                                            <br>
                                                            <span class="text-danger err"></span>
                                                        </td>
                                                        <td>
                                                            <select name="bhk[1][]" class="block-field">
                                                                <option value="1">1 BHK</option>
                                                                <option value="2">2 BHK</option>
                                                                <option value="3">3 BHK</option>
                                                                <option value="4">4 BHK</option>
                                                                <option value="5">5 BHK</option>
                                                            </select>
                                                            <br>
                                                            <span class="text-danger err"></span>
                                                        </td>
                                                        <td>
                                                            <a href="javascript:void(0)" data-tower-number="1"
                                                                class="btn btn-primary btn-sm add_property_row">
                                                                +
                                                            </a>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td colspan="7">
                                                            <button type="button" class="deleteBlock btn btn-danger mt-2" style="width:100%">Delete</button>
                                                        </td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `);


                // let csrfToken = $('#societyAddForm input[name="_token"]').val();

                // $('#societyAddForm')[0].reset();
                // $('#societyAddForm input, #societyAddForm select, #societyAddForm textarea').val('');
                // Reset the form, but exclude specific fields by name
                $('#societyAddForm').find(
                    'input:not([name="_token"],[name^="amenities[]"],[name^="unit_type"]), select, textarea').each(
                    function() {
                        $(this).val('');
                    });
                $('#societyAddForm').find('input[name="amenities[]"]').prop('checked', false);
                $('.showBlockName').text('');
                $('select[name="bhk[1][]"]').css('display', '');

                $('#societyAddForm select').each(function() {
                    $(this).prop('selectedIndex', 0); // Select the first option
                });
                // $('#societyAddForm input[name="_token"]').val(csrfToken);
                $('#societyTab1-tab').trigger('click');

            });

            $('.edit').on('click', function() {
                // loader
                $('#loader').css('width', '50%');
                $('#loader').fadeIn();
                $('#blockOverlay').fadeIn();

                // $('#societyAddForm')[0].reset();
                // $('#societyAddForm input, #societyAddForm select, #societyAddForm textarea').val('');
                $('#societyAddForm').find(
                    'input:not([name="_token"],[name^="amenities[]"],[name^="unit_type"]), select, textarea').each(
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
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        $('#societyTab1-tab').trigger('click');
                        let data = res.data;
                        if (res.status == 'error') {
                            toastr[res.status](res.message);
                        }
                        $('#societyAddForm').attr('action',
                            '{{ route($thisModule . '.society.update', ['id' => '__ID__']) }}'
                            .replace('__ID__', data.id));
                        $('#societyAddForm2').attr('data-formtype', 'edit');
                        if(data.status == 'inactive'){

                            $('#societyAddForm2').text('Update And Publish');
                            $('#societyAddFormDraft').text('Update Draft');
                        }else{
                            $('#societyAddFormDraft').hide();
                            $('#societyAddForm2').text('Update');

                        }

                        // modal data
                        // const assignAdminSelect = $('#assignAdmin');
                        // assignAdminSelect.empty();
                        // assignAdminSelect.append(
                        //     '<option value="" selected>--select--</option>');
                        // console.log(res.admins);

                        // // Check if response.admins is not empty and an array
                        // if (Array.isArray(res.admins) && res.admins.length > 0) {
                        //     // Loop through the admins and create new options
                        //     res.admins.forEach(function(admin) {
                        //         assignAdminSelect.append(
                        //             `<option value="${admin.user_id}">${admin.name}</option>`
                        //         );
                        //     });
                        // } else {
                        //     // Optionally, add a message if there are no admins
                        //     assignAdminSelect.append(
                        //         '<option value="" disabled>No admins available</option>');
                        // }

                        $('#sname').val(data.name);
                        $('#location').val(data.location);
                        $('#city').val(data.city);
                        $('#state').val(data.state).trigger('change');
                        $('#pin').val(data.pin);
                        $('#societyContact').val(data.contact);
                        $('#societyEmail').val(data.email);
                        $('#reg').val(data.registration_num);
                        $('#statusSelect').val(data.status);
                        $('#societyArea').val(data.total_area);
                        $('#totalTowers').val(data.total_towers);
                        $('#societyTypeSelect').val(data.type);
                        // let amenities = data.amenities.split(',');
                        let amenities = data.amenities ? data.amenities.split(',') : [];
                        $('input[type="checkbox"][data-amenity]').each(function() {
                            let amenity = $(this).data('amenity'); // Get the value of data-amenity
                            if (amenities.includes(amenity)) {
                                $(this).prop('checked', true); // Check the checkbox if it matches
                            }
                        });
                        $('#totalTowers').val(data.total_towers);

                        $('#floors').val(data.floors);
                        $('#floorUnits').val(data.floor_units);
                        // $('#assignAdmin').val(data.member_id);
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
                                                            class="form-control phone-input-restrict">
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
                            // var totalUnits = blocks[0].total_units;
                            var totalfloors = blocks[0].total_floor;
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
                                                                        <label>Tower Name/Block Name</label>
                                                                        <input type="text" name="bname[${blockIndex}]"
                                                                            class="form-control" value="${blockName}">

                                                                        <span class="text-danger err"></span>
                                                                    </div>
                                                                </div>
                                                                <div class="col">
                                                                    <div class="form-group">
                                                                        <label>Total Units</label>
                                                                        <input type="text" name="totalFloors[${blockIndex}]"
                                                                            class="form-control" value="${totalfloors}">

                                                                        <span class="text-danger err"></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="blocks_table">
                                                        <div class="table-responsive">
                                                            <table width="100%" cellpadding="0" cellspacing="0">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Property Number</th>
                                                                        <th>Floor/Unit No.</th>
                                                                        <th>Property Type</th>
                                                                        <th class="d-none">Ownership</th>
                                                                        <th>Size (Sq.Yard) </th>
                                                                        <th>BHK</th>
                                                                        <th>&nbsp; </th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody id="tower_property_${blockIndex}">
                                                                    ${generateUnitRows(blocks, blockIndex)}
                                                                    <tfoot>
                                                                        <tr>
                                                                            <td colspan="7">
                                                                                <button type="button"
                                                                                class="deleteBlock btn btn-danger mt-2"
                                                                                style="width:100%">Delete</button>
                                                                            </td>
                                                                        </tr>
                                                                    </tfoot>
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
                            let tcount = 0;
                            $.each(blocks, function(i, block) {
                                tcount++;
                                rows += `
                                    <tr>
                                        <td>
                                            <input type="hidden" name="block_id[${blockIndex}][]" value="${block.id}">
                                            <input type="text"
                                                name="property_number[${blockIndex}][]" value="${block.property_number}" class="block-field" style="width: 100%;">
                                            <br>
                                            <span class="text-danger err"></span>
                                        </td>
                                        <td>
                                            <input type="text"
                                                name="property_floor[${blockIndex}][]"
                                                class="block-field" value="${block.floor}">
                                            <br>
                                            <span class="text-danger err"></span>
                                        </td>
                                        <td>
                                            <select name="property_type[${blockIndex}][]" class="block-field " id="">
                                                <option value="residential" ${block.unit_type === 'residential' ? 'selected' : ''}>Residential</option>
                                                <option value="commercial" ${block.unit_type === 'commercial' ? 'selected' : ''}>Commercial</option>
                                            </select>
                                            <br>
                                            <span class="text-danger err"></span>
                                        </td>
                                        <td class="d-none">
                                            <select name="ownership[${blockIndex}][]" class="block-field" id="">
                                                <option value="vacant" ${block.ownership === 'vacant' ? 'selected' : ''}>Vacant</option>
                                                <option value="occupied" ${block.ownership === 'occupied' ? 'selected' : ''}>Occupied</option>
                                            </select>
                                            <br>
                                            <span class="text-danger err"></span>
                                        </td>
                                        <td>
                                            <input type="text"
                                                name="unit_size[${blockIndex}][]"
                                                class="block-field" value="${block.unit_size}">
                                            <br>
                                            <span class="text-danger err"></span>
                                        </td>
                                        <td>
                                            <select name="bhk[${blockIndex}][]" data-previous-bhk="${block.bhk}" class="block-field" id=""
                                            style="${block.unit_type === 'commercial' ? 'display: none;' : ''}">
                                                <option value="1" ${block.bhk === '1' ? 'selected' : ''}>1 BHK</option>
                                                <option value="2" ${block.bhk === '2' ? 'selected' : ''}>2 BHK</option>
                                                <option value="3" ${block.bhk === '3' ? 'selected' : ''}>3 BHK</option>
                                                <option value="4" ${block.bhk === '4' ? 'selected' : ''}>4 BHK</option>
                                                <option value="5" ${block.bhk === '5' ? 'selected' : ''}>5 BHK</option>
                                            </select>
                                            <br>
                                            <span class="text-danger err"></span>
                                        </td>
                                        <td>
                                            <a href="javascript:void(0)" data-tower-number="${blockIndex}"
                                            class="${tcount == 1 ? 'btn btn-primary btn-sm add_property_row' : 'btn btn-danger remove_property_row'}">
                                                ${tcount == 1 ? '+' :   '-'}
                                            </a>
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
    <script>
        $(document).ready(function () {
            // Use event delegation on a parent container (e.g., 'body')
            // $(document).on('input', 'input[name^="emr_phone["]', function () {
            //     // Allow only numeric input and limit to 10 characters
            //     this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);
            // });
            // $('input.phone-input-restrict').on('input', function () {
            //     // Restrict to numeric input and limit the length to 10 digits
            //     this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);
            // });
            $(document).on('input', '.phone-input-restrict', function () {
                // Restrict to numeric input and limit to 10 digits
                this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);
            });

            $(document).on('input', '.email-input-restrict', function () {
                // Allow only valid email characters
                this.value = this.value.replace(/[^a-zA-Z0-9@._-]/g, '');
            });

            // $('input.email-input-restrict').on('input', function () {
            //     this.value = this.value.replace(/[^a-zA-Z0-9@._-]/g, '');
            // });

        });
        $(document).on('change', 'select[name^="property_type"]', function() {
            let selectedValue = $(this).val(); // Get the selected value
            let blockIndex = $(this).attr('name').match(/\[(\d+)\]/)[1]; // Extract blockIndex from the name attribute

            // Find the corresponding <td> with the bhk field
            let bhkField = $(this).closest('tr').find(`select[name="bhk[${blockIndex}][]"]`);

            if (selectedValue === 'commercial') {

                if (bhkField.find('option[value=""]').length === 0) {
                    bhkField.prepend('<option value="">none</option>');
                }
                bhkField.val(''); // Clear the value
                bhkField.hide(); // Hide the <td>
            } else {
                bhkField.show(); // Show the <td>
                // Remove the "none" option if it exists
                bhkField.find('option[value=""]').remove();

                // Restore the previously selected option
                let previouslySelectedValue = $(this).data('previous-bhk') || bhkField.find('option:first').val();
                bhkField.val(previouslySelectedValue);

                $(this).data('previous-bhk', bhkField.val());
                // bhkField.prop('selectedIndex', 0);
            }
        });
        $(document).ready(function() {
            $('#state').select2({
                width: '100%',
                dropdownParent: $('.modal-content'),
                minimumResultsForSearch: 0 // Always show the search box
            });
        });
        $(document).ready(function () {
            $("#city").on("keyup", function () {
                let place = $(this).val();

                if (place.length > 2) { // Start searching after 3 characters
                    $.ajax({
                        url: "{{ route($thisModule . '.getStateByPlaceMatch')}}",
                        type: "GET",
                        data: { place: place },
                        success: function (data) {
                            if (data.state) {
                                $("#state").val(data.state).trigger("change");
                            }
                        }
                    });
                }
            });
        });
    </script>
@endpush
