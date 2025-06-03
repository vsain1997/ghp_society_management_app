@extends($thisModule . '::layouts.master')

@section('title', 'Members')

@section('content')

    <div class="right_main_body_content members_page">
        <div class="head_content">
            <div class="left_head">
                <h2>Members</h2>
                <p>Add or manage members of the society</p>
            </div>
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

                 <button type="button" id="openModal" class="bg_theme_btn" data-bs-toggle="modal" data-bs-target="#addMemberModal">
                    <svg width="14" height="15" viewBox="0 0 14 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M13.0005 8.5H8.00049V13.5C8.00049 14.05 7.55049 14.5 7.00049 14.5C6.45049 14.5 6.00049 14.05 6.00049 13.5V8.5H1.00049C0.450488 8.5 0.000488281 8.05 0.000488281 7.5C0.000488281 6.95 0.450488 6.5 1.00049 6.5H6.00049V1.5C6.00049 0.95 6.45049 0.5 7.00049 0.5C7.55049 0.5 8.00049 0.95 8.00049 1.5V6.5H13.0005C13.5505 6.5 14.0005 6.95 14.0005 7.5C14.0005 8.05 13.5505 8.5 13.0005 8.5Z"
                            fill="white" />
                    </svg>
                    Add New Member
                </button>
            </div>

           
        </div>
        <div class="custom_table_wrapper">
            <div class="filter_table_head">
                <div class="search_wrapper search-members-gstr">
                    <form action="{{ route($thisModule . '.member.index') }}" method="GET">
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
                            <input type="hidden" name="sid" value="{{ session('__selected_society__') }}">
                            <div class="filter-secl">
                                <select name="status" class="form-control">
                                    <option value="none" disabled>--Choose Status--</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active
                                    </option>
                                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>
                                        Inactive
                                    </option>
                                </select>

                                <select name="search_for" class="form-control">
                                    <option value="none" disabled>--Search By--</option>
                                    <option value="name" {{ request('search_for') == 'name' ? 'selected' : '' }}>Name
                                    </option>
                                    <option value="phone" {{ request('search_for') == 'phone' ? 'selected' : '' }}>
                                        Contact
                                    </option>
                                    <option value="email" {{ request('search_for') == 'email' ? 'selected' : '' }}>
                                        Email
                                    </option>
                                    <option value="aprt_no" {{ request('search_for') == 'aprt_no' ? 'selected' : '' }}>
                                        Property Number
                                    </option>
                                </select>
                                 
                                <select name="tower" id="tower" class=" form-select form-control">
                                    <option value="">--Select--</option>
                                    @foreach($blocks as $block)
                                    @if (request('tower') && request('tower') == $block->name)
                                    <option value="{{ $block->name }}" selected>{{ $block->name }}</option>
                                    @else
                                    <option value="{{ $block->name }}">{{ $block->name }}</option>
                                    @endif
                                    @endforeach
                                </select>
                                <span class="text-danger" id="tower_error"></span>
                                
                            </div>

                            <div class="search-full-box">
                                <input type="search" name="search" id="search" placeholder="Search"
                                    value="{{ request('search') }}">
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
                                <a href="{{ route($thisModule . '.member.index') }}" class="resetbtn" style="font-size: 18px; background: #4b40b5; color: white; padding: 9px 15px; border-radius: 6px; margin-left: 7px;">Reset</a>

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
                            <th class="text-center">Tower/Block</th>
                            <th class="text-center">Property Number</th>
                            <th class="text-center">Name</th>
                            <th class="text-center">Role</th>
                            
                            <!-- <th class="text-center">Floor</th> -->
                            <th class="text-center">Property Type/Unit Type</th>                            
                            <th class="text-center">Maintenance Bill</th>
                            <th class="text-center">Contact</th>
                            <th class="text-center">Email</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Actions</th>
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
                                    <td class="text-center">{{ $member->block->name ?? '' }}</td>
                                    <td class="text-center">{{ $member->aprt_no }}</td>
                                    <td class="text-center">{{ $member->name }}</td>
                                    <td class="text-center"><?= ucfirst($member->role)?></td>
                                    <!-- <td class="text-center">{{ $member->floor_number }}</td> -->
                                    <td class="text-center"><?= ucfirst($member->unit_type) ?></td>                                    
                                    <td class="text-center">{{ toRupeeCurrency($member->maintenance_bill) ?? '--' }}</td>
                                    <td class="text-center">{{ $member->phone }}</td>
                                    <td class="text-center" title="{{ $member->email }}">{{ $member->email }}</td>
                                    <td class="text-center">
                                        <input type="hidden" name="statusVal"
                                            value="{{ parseStatus($member->status, 0) }}">
                                        <div class="status_select">
                                            <select name="status" data-id="{{ $member->id }}"
                                                class="statusOption form-select">
                                                <option value="active" {{ parseStatus($member->status, 1) }}>Active
                                                </option>
                                                <option value="inactive" {{ parseStatus($member->status, 2) }}>Inactive</option>
                                            </select>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="actions">
                                            <a href="javascript:void(0)" id="{{ $member->id }}" class="edit">
                                                <img src="{{ url($thisModule) }}/img/edit.png" alt="edit">
                                            </a>
                                            <a class="view" href="{{ route($thisModule . '.member.details', ['id' => $member->id]) }}"
                                                id="{{ $member->id }}">
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
                                <td colspan="10" class="text-center"> No Data Found </td>
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
                            <input type="hidden" name="user_id" id="user_id">
                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <label for="name">Name <span class="text-danger">*</span></label>
                                        <input type="text" name="name" id="name" class="form-control">
                                        <span class="text-danger err"></span>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <label for="role">Role <span class="text-danger">*</span></label>
                                        <select name="role" id="role" class="form-select form-control ">
                                            <option value="" selected>--select--</option>
                                            <option value="resident">Resident</option>
                                            <option value="admin">Admin</option>
                                        </select>
                                        <span class="text-danger err"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row pswd_block">
                                <div class="col">
                                    <div class="form-group">
                                        <label for="password">Password <span class="text-danger">*</span></label>
                                        <input type="text" name="password" id="password" class="form-control">
                                        <span class="text-danger err"></span>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <label for="password_confirmation">Confirm Password <span class="text-danger">*</span></label>
                                        <input type="text" name="password_confirmation" id="password_confirmation"
                                            class="form-control">
                                        <span class="text-danger err"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <label for="phone">Contact <span class="text-danger">*</span></label>
                                        <input type="text" name="phone" id="phone" value=""
                                            onkeyup="checkDuplicate('phone',this)" class="form-control phone-input-restrict">
                                        <span class="text-danger err"></span>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <label for="email">Email <span class="text-danger">*</span></label>
                                        <input type="text" name="email" id="email" value=""
                                            onkeyup="checkDuplicate('email',this)" class="form-control">
                                        <span class="text-danger err"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <label for="society_id">Society <span class="text-danger">*</span></label>
                                        <select name="society_id" id="society_id" class="form-select form-control"
                                            onchange="showBlocks(this)">
                                            {{-- <option value="">--select--</option> --}}
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
                                                    {{-- <option value="{{ $society->id }}">{{ $society->name }}
                                                    </option> --}}
                                                @endif
                                                @php $cnt1++; @endphp
                                            @endforeach
                                        </select>
                                        <span class="text-danger err"></span>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <label for="block_id">Tower <span class="text-danger">*</span></label>
                                        <select id="block_id" class="form-select form-control " onchange="showBlocks(this,null,'blockEvent')">
                                            <option value="" selected>--select--</option>
                                        </select>
                                        <span class="text-danger err"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <label for="aprt_no">Property Number <span class="text-danger">*</span></label>
                                        <select name="aprt_no" id="aprt_no" class="form-select form-control ">
                                            <option value="" selected>--select--</option>
                                        </select>
                                        <span class="text-danger err"></span>
                                    </div>
                                </div>

                                <div class="col">
                                    <div class="form-group">
                                        <label for="ownership">Ownership <span class="text-danger">*</span></label>
                                        <select name="ownership" id="ownership" class="form-control ">
                                            <option value="" selected>--select--</option>
                                            <option value="owned">Owned</option>
                                            <option value="rented">Rented</option>
                                        </select>
                                        <span class="text-danger err"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="maintenance_bill">Maintenance Bill <span class="text-danger">*</span></label>
                                        <input type="text" name="maintenance_bill" id="maintenance_bill" class="form-control">
                                        <span class="text-danger err"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="row rentedOwnerBlock">
                                <div class="col">
                                    <div class="form-group">
                                        <label for="owner_name">Owner Name <span class="text-danger">*</span></label>
                                        <input type="text" name="owner_name" id="owner_name" class="form-control">
                                        <span class="text-danger err"></span>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <p class="pb-1">Emergency Contact </p>
                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <label for="emer_name">Name</label>
                                        <input type="text" name="emer_name" id="emer_name" class="form-control">
                                        <span class="text-danger err"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <label for="emer_relation">Relation</label>
                                        <input type="text" name="emer_relation" id="emer_relation" class="form-control">
                                        <span class="text-danger err"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <label for="emer_phone">Phone</label>
                                        <input type="text" name="emer_phone" id="emer_phone" class="form-control phone-input-restrict">
                                        <span class="text-danger err"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col text-end">
                                    <button type="button" class="border_theme_btn cancel_btn"
                                        data-bs-dismiss="modal">Close</button>
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
            // set modal form
            $('#addMemberForm').attr('action',
                '{{ route($thisModule . '.member.store') }}'
            );
            $('#submitAddMemberForm').attr('data-formtype', 'add');
            $('#modalHeadTxt').text('Add Member');
            $('#submitAddMemberForm').text('Submit');

            $('#aprt_no').empty();

            // reset form data
            $('#addMemberForm').find(
                'input:not([name="_token"],[name^="unit_type"],[name^="society_id"],[name^="ownership"]), select, textarea').each(
                function() {
                    $(this).val('');
                });

            $('#society_id').val({{ session('__selected_society__') }});
            showBlocks({{ session('__selected_society__') }}, function() {
                // alert({{ session('__selected_society__') }});
            });
            $('.err').text('');

            $('.rentedOwnerBlock').hide();

            // $('#addMemberForm select').each(function() {
            //     $(this).prop('selectedIndex', 0); // Select the first option
            // });

        });

        function showBlocks(s, callback,blockid = null,$fetchBlockData = null) {
            let societyId = 0;
            let blockID = null;
            if (callback && typeof callback === 'function') {

                societyId = s;
            } else {
                if (blockid == null) {
                    societyId = Number(s.value);
                    console.log('if',societyId);
                }else{
                    societyId = Number($('#society_id').val());
                    blockID = s.value;
                    console.log('else',blockID,societyId);
                }
            }
            let user_id = 0;
            user_id = Number($('#user_id').val());
            let url = '{{ route($thisModule . '.get.blocks') }}';
            let method = 'POST';
            let headers = {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            };
            let body = {
                society_id: societyId,
                block_id:blockID,
                user_id:user_id,
            };

            fetchData(url, method, headers, body)
                .then(data => {
                    if (data) {
                        // Create sets to keep track of unique block names and unit types
                        const uniqueBlockNames = new Set();
                        const uniquePropertyNumbers = new Set();

                        // Handle success response
                        if (data.status === 'success') {
                            // Loop through the blocks and append them to the dropdowns
                            if(blockid == null){

                                $('#block_id').empty();
                                $.each(data.getUniqueBlocks, function(index, block) {
                                    // Add unique block names to #block_id dropdown
                                    if (!uniqueBlockNames.has(block.name)) {
                                        uniqueBlockNames.add(block.name); // Add to the set
                                        $('#block_id').append('<option value="' + block.id + '">' + block.name + '</option>');
                                    }
                                });
                            }

                            // Loop through availableProperties and append property_numbers to #aprt_no dropdown

                            $('#aprt_no').empty();
                            if (data.availableProperties.length == 0) {
                                $('#aprt_no').append('<option value="" >No vacant property left</option>');
                            } else {
                                $.each(data.availableProperties, function(index, block) {
                                    // Add unique property numbers to #aprt_no dropdown
                                    if (!uniquePropertyNumbers.has(block.property_number)) {
                                        uniquePropertyNumbers.add(block.property_number); // Add to the set
                                        $('#aprt_no').append('<option value="' + block.id + '">' + block.property_number + '</option>');
                                    }
                                });
                            }
                            console.log('block----------','len',data.availableProperties.length);
                        } else {
                            // If status is not 'success', show an error message
                            $('#block_id').append('<option value="">Not Available</option>');
                            $('#aprt_no').append('<option value="">Not Available</option>');
                        }

                        if (callback && typeof callback === 'function') {
                            callback();
                            console.log('callback trigger----------');

                            if ($fetchBlockData) {
                                let $blockSelect = $('#block_id');
                                // console.log("Existing options before filtering:", $blockSelect.html());
                                // Step 1: Store all existing options
                                let options = [];
                                $blockSelect.find('option').each(function () {
                                    let value = $(this).val();
                                    let text = $(this).text();
                                    // Step 2: Remove the option that matches $fetchBlockData.name
                                    if (text.trim() !== $fetchBlockData.name.trim()) {
                                        options.push({ value, text });
                                    } else {
                                        // console.log("Removed option:", { value, text });
                                    }
                                });
                                // console.log("Filtered options:", options);
                                // Step 3: Clear all options
                                $blockSelect.empty();
                                // console.log("Dropdown cleared");
                                // Step 5: Append the new option
                                $blockSelect.append('<option value="' + $fetchBlockData.id + '">' + $fetchBlockData.name + '</option>');
                                // Step 4: Re-add the filtered options
                                options.forEach(opt => {
                                    $blockSelect.prepend('<option value="' + opt.value + '">' + opt.text + '</option>');
                                });
                                // console.log("Re-added filtered options:", $blockSelect.html());
                                // console.log("Final dropdown options:", $blockSelect.html());
                                // console.log("New option added:", { id: $fetchBlockData.id, name: $fetchBlockData.name });
                            }
                        }
                    } else {
                        // Handle the case where no data is returned
                        console.log('No data received from the server.');
                    }
                }).catch(error => {
                    // Handle network or other errors
                    console.error('Error fetching data:', error);
                });
        }

        async function checkDuplicate(field, value, userId = null, objectParam = null) {
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
            } else if (field === 'apert_no') {
                url = '{{ route($thisModule . '.check.user.apertmentNo') }}';
                body = {
                    apert_no: value,
                    userId: userId,
                };
            } else if (field === 'isAlreadyAdminExist') {
                url = '{{ route($thisModule . '.check.user.isAlreadyAdminExist') }}';
                body = {
                    society_id: value,
                    userId: userId,
                };
                // console.log("isAlreadyAdminExist");
                // console.log("hi", JSON.stringify(body));

            } else if (field === 'checkVacancy') {
                url = '{{ route($thisModule . '.check.user.checkVacancy') }}';
                body = {
                    aprt_no: value,
                    user_id: userId,
                    block_id: objectParam.block_id,
                    society_id: objectParam.society_id,
                };
            }

            // console.log("isAlreadyAdminExistEnd");

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
            // disable modal outside click
            $('#addMemberModal').modal({
                backdrop: 'static',
                keyboard: false
            });
            //onchange id=role, when role == admin,show class="pswd_block" , else hide
            $('.pswd_block').hide();
            $('#addMemberForm').on('change', '#role', function() {
                let formType = $('#user_id').val();
                // if (formType && formType > 0) {
                //     //edit
                //     // $('.pswd_block').hide();

                // } else {
                //     // add
                //     if ($(this).val() == 'admin') {
                //         $('.pswd_block').show();
                //     } else {
                //         $('.pswd_block').hide();
                //     }
                // }
                if ($(this).val() == 'admin') {
                    $('.pswd_block').show();
                } else {
                    $('.pswd_block').hide();
                }
            });

            // check form1 validation and switch to next form2
            $(".modal").on('click', '#submitAddMemberForm', async function(event) {
                event.preventDefault();
                // loader add
                $('#loader').css('width', '50%');
                $('#loader').fadeIn();
                $('#blockOverlay').fadeIn();

                // let formType = $('#submitAddMemberForm').data('formtype');
                let formType = $('#user_id').val();
                if (formType > 0 && formType) {
                    formType = 'edit';
                } else {
                    formType = 'add';
                }
                console.log('call', formType);

                let validationStatus = await validateForm(formType);
                if (validationStatus != 0) {
                    //loader removed
                    $('#loader').css('width', '100%');
                    $('#loader').fadeOut();
                    $('#blockOverlay').fadeOut();

                    if (validationStatus == 5) {
                        return false; //restrict other alert messages
                    }
                    // toastr.error('Kindly complete all fields accurately !')
                    return false;
                }

                //direct submit for add and ajax submit for edit
                if (formType == 'add') {
                    //loader removed
                    $('#loader').css('width', '100%');
                    $('#loader').fadeOut();
                    $('#blockOverlay').fadeOut();

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
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        },
                        success: function(response) {
                            //loader removed
                            $('#loader').css('width', '100%');
                            $('#loader').fadeOut();
                            $('#blockOverlay').fadeOut();

                            toastr[response.status](response.message);
                            setTimeout(function() {
                                location.reload();
                            }, 2000);
                        },
                        error: function(xhr, status, error) {
                            //loader removed
                            $('#loader').css('width', '100%');
                            $('#loader').fadeOut();
                            $('#blockOverlay').fadeOut();

                            toastr[response.status](response.message);
                        }
                    });
                }
            });
        });

        //validate
        async function validateForm(formType) {
            let hasError = 0;
            // console.log('called', formType);

            // Assign form values to variables
            let userId = $('#user_id').val().trim();
            let name = $('#name').val().trim();
            let role = $('#role').val();
            let phone = $('#phone').val().trim();
            let email = $('#email').val().trim();
            let societyId = $('#society_id').val();
            let blockId = $('#block_id').val();
            let aprtNo = $('#aprt_no').val().trim();
            let ownership = $('#ownership').val().trim();
            let maintenance_bill = $('#maintenance_bill').val().trim();
            let owner_name = $('#owner_name').val().trim();
            let emer_name = $('#emer_name').val().trim();
            let emer_relation = $('#emer_relation').val().trim();
            let emer_phone = $('#emer_phone').val().trim();


            // console.log(formType);
            // console.log(userId);
            let resultPhone;
            let resultEmail;
            let isAlreadyAdminExist;
            // let vacancyInfo;
            let societyInfo = {
                block_id: blockId,
                society_id: societyId,
                aprt_no: aprtNo,
            };
            if (formType == 'add') {
                resultPhone = await checkDuplicate('phone', phone);
                resultEmail = await checkDuplicate('email', email);
                // resultApertNo = await checkDuplicate('apert_no', aprtNo);
                // vacancyInfo = await checkDuplicate('checkVacancy', aprtNo, null, societyInfo);
                if (role == 'admin') {

                    isAlreadyAdminExist = await checkDuplicate('isAlreadyAdminExist', societyId);
                }
            } else {
                resultPhone = await checkDuplicate('phone', phone, userId);
                resultEmail = await checkDuplicate('email', email, userId);
                // resultApertNo = await checkDuplicate('apert_no', aprtNo, userId);
                // vacancyInfo = await checkDuplicate('checkVacancy', aprtNo, userId, societyInfo);
                if (role == 'admin') {

                    isAlreadyAdminExist = await checkDuplicate('isAlreadyAdminExist', societyId, userId);
                }
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
            } else if (isAlreadyAdminExist) {

                $('#role').next('.err').text('Admin Already exits !');
                hasError = 1;
            }

            var password = $('#password').val();
            if (role === 'admin' && password != '') {
                // Get password and confirm password values
                var passwordConfirmation = $('#password_confirmation').val();

                if (password === '') {
                    $('#password').next('.err').text('Password is required');
                    hasError = 1;
                }
                // Validate password length (at least 8 characters)
                if (password.length < 8) {
                    $('#password').next('.err').text('Minimum 8 characters');
                    hasError = 1;
                }

                // Validate password match
                if (password !== passwordConfirmation) {
                    $('#password_confirmation').next('.err').text('Passwords do not match.');
                    hasError = 1;
                }
            }

            // Validate phone
            if (phone === '') {
                $('#phone').next('.err').text('Required');
                hasError = 1;
            } else if (!validatePhone(phone)) {
                $('#phone').next('.err').text('Invalid');
                hasError = 1;
            } else if (resultPhone) {
                $('#phone').next('.err').text('Already exists !');
                hasError = 1;
            }

            // Validate email
            if (email === '') {
                $('#email').next('.err').text('Required');
                hasError = 1;
            } else if (!validateEmail(email)) {
                $('#email').next('.err').text('Invalid');
                hasError = 1;
            } else if (resultEmail) {
                $('#email').next('.err').text('Already exists !');
                hasError = 1;
            }

            // Validate society_id
            if (societyId === '' || isNaN(societyId)) {
                $('#society_id').next('.err').text('Required');
                hasError = 1;
            }

            // Validate block_id
            if (blockId === '' || isNaN(blockId)) {
                $('#block_id').next('.err').text('Required');
                hasError = 1;
            }

            // Validate aprt_no
            if (aprtNo === '') {
                $('#aprt_no').next('.err').text('Required');
                hasError = 1;
            }

            if (maintenance_bill === '') {
                $('#maintenance_bill').next('.err').text('Required');
                hasError = 1;
            }

            if (ownership === '') {
                $('#ownership').next('.err').text('Required');
                hasError = 1;
            }

            if (ownership === '') {
                $('#ownership').next('.err').text('Required');
                hasError = 1;
            }

            if (ownership === 'rented' && owner_name === '') {
                $('#owner_name').next('.err').text('Required');
                hasError = 1;
            }

            if (isNotEmpty(emer_name) && !isNaN(emer_name)) {
                $('#emer_name').next('.err').text('Invalid');
                hasError = 1;
            }

            if (isNotEmpty(emer_relation) && !isNaN(emer_relation)) {
                $('#emer_relation').next('.err').text('Invalid');
                hasError = 1;
            }

            $phoneError = $('#emer_phone').next('.err');
            if (isNotEmpty(emer_phone) && isInvalidPhone(emer_phone, $phoneError, phoneErrMsg_invalid = "Invalid")) {
                hasError = 1;
            }


            // else if (resultApertNo) {
            //     $('#aprt_no').next('.err').text('Already occupied !');
            //     hasError = 1;
            // }

            // vacancy check
            // if (vacancyInfo) {
            //     toastr.error('All apartments are occupied ');
            //     hasError = 5;
            // }

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
                // $('.pswd_block').hide();
                // loader add
                $('.err').text('');
                $('#loader').css('width', '50%');
                $('#loader').fadeIn();
                $('#blockOverlay').fadeIn();

                $('#addMemberForm')[0].reset();
                $('#addMemberForm').find(
                    'input:not([name="_token"],[name^="unit_type"]), select, textarea').each(
                    function() {
                        $(this).val('');
                    });

                $('#addMemberForm select').each(function() {
                    $(this).prop('selectedIndex', 0); // Select the first option
                });
                // disable outside click + exc press
                $('#addMemberModal').modal({
                    backdrop: 'static',
                    keyboard: false
                })
                // change modal heading
                $('#modalHeadTxt').text('Edit Member');
                $('#submitAddMemberForm').text('Update');

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
                        $('#user_id').val(data.user_id);
                        $('#name').val(data.name);
                        $('#role').val(data.role);
                        $('#phone').val(data.phone);
                        $('#email').val(data.email);
                        $('#owner_name').val(data.owner_name);
                        $('#emer_name').val(data.emer_name);
                        $('#emer_relation').val(data.emer_relation);
                        $('#emer_phone').val(data.emer_phone);
                        $('#society_id').val(data.society_id);
                        $('#maintenance_bill').val(data.maintenance_bill);

                        if (data.role == 'admin') {
                            $('.pswd_block').show();
                        } else {
                            $('.pswd_block').hide();
                        }

                        // Call showBlocks(s) and use a callback to ensure it completes first
                        showBlocks(data.society_id, function() {
                            // console.log('success ajx');

                            // After showBlocks is done, set the other values
                            $('#block_id').val(data.block_id);//.trigger('change');
                            console.log('fetch',data.block_id);

                            // $('#aprt_no').val(data.block_id).trigger('change');
                            $('#aprt_no').append(
                                `<option value="${data.block_id}">${data.aprt_no}</option>`
                            ).val(data.block_id).trigger('change');
                            $('#ownership').val(data.ownership_type).trigger('change');
                        },null,data.block);


                        //loader removed
                        $('#loader').css('width', '100%');
                        $('#loader').fadeOut();
                        $('#blockOverlay').fadeOut();

                        $('#addMemberModal').modal('show');
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
            let nowStts = $(this).val();
            let $this = $(this);
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
                        url: "{{ route($thisModule . '.member.status.change', ['id' => ':memberId', 'status' => ':toStatus']) }}"
                            .replace(':memberId', memberId).replace(':toStatus', toStatus),
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
                    $this.val(preSttsS); // Reset the status option
                    $hiddenInput.val(preSttsN); // Reset the hidden input value
                }
            });
        });
    </script>
    <script>
        $(document).ready(function () {
            // Initially hide the block
            $('.rentedOwnerBlock').hide();

            $('#ownership').on('change', function () {
                if ($(this).val() === 'rented') {
                    $('.rentedOwnerBlock').show();
                } else {
                    $('.rentedOwnerBlock').hide();
                }
            });
        });
    </script>
@endpush
