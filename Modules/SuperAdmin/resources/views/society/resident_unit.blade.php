@extends($thisModule . '::layouts.master')

@section('title', 'Society Properties')

@section('content')
<div class="right_main_body_content members_page">

    <div class="head_content">
        <div class="left_head">
            <h2>Society Properties</h2>
        </div>
    </div>

    <div class="custom_table_wrapper">
        <div class="filter_table_head visitor-sec">
            <div class="search_wrapper search-members-gstr">
                <form method="GET">
                    <div class="input-group">
                        <div class="filter-box">
                            <div class="filter-secl">
                                <label for="occupancy">Occupancy</label>
                                <select name="occupancy" id="occupancy" class=" form-select form-control">
                                    <option value="">All</option>
                                    <option value="occupied" {{ request('occupancy')=='occupied' ? 'selected' : '' }}>
                                        Occupied</option>
                                    <option value="vacant" {{ request('occupancy')=='vacant' ? 'selected' : '' }}>Vacant
                                    </option>
                                </select>
                                <span class="text-primary err" id="aprtNoShowData"></span>
                            </div>
                            <div class="filter-secl">
                                <label for="tower">Tower</label>
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
                            <div class="filter-secl">
                                <label for="floor">Floor</label>
                                <select name="floor" id="floor" class=" form-select form-control">
                                    <option value="">--Select--</option>
                                    @foreach($floors as $fld)
                                    @if (request('floor') && request('floor') == $fld->floor)
                                    <option value="{{ $fld->floor }}" selected>{{ $fld->floor }}</option>
                                    @else
                                    <option value="{{ $fld->floor }}">{{ $fld->floor }}</option>
                                    @endif
                                    @endforeach
                                </select>
                                <span class="text-danger" id="floor_error"></span>
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
            <div class="right_filters d-none">
                <h2>Society Properties </h2>
                <button class="filterbtn">
                    <svg width="14" height="12" viewBox="0 0 14 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12.8327 0.75H1.16602L5.83268 6.26833V10.0833L8.16602 11.25V6.26833L12.8327 0.75Z"
                            stroke="#595959" stroke-width="1.16667" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>

                    Filter
                </button>
            </div>
        </div>
        <div class="table-responsive">
            <table width="100%" cellpadding="0" cellspacing="0">
                <thead>
                    <tr>
                        <th class="text-center">Tower</th>
                        <th class="text-center">Property Number</th>
                        <th class="text-center">Property Type</th>
                        <th class="text-center">Floor</th>
                        <th class="text-center">Size (Sq. Ft.)</th>
                        <th class="text-center">Ownership </th>
                        <th class="text-center">Name of Occupant </th>
                        <th class="text-center">Number</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $sl = 0;
                    // dd($datas);
                    @endphp
                    @if ($datas && !$datas->isEmpty())
                    @foreach ($datas as $data)
                    <td class="text-center">
                        {{ !empty($data->name) ? $data->name : '' }}
                    </td>
                    <td class="text-center">
                        {{ !empty($data->property_number) ? $data->property_number : '' }}
                    </td>
                    <td class="text-center">
                        {{ !empty($data->unit_type) ? ucfirst($data->unit_type) : '' }}
                    </td>
                    <td class="text-center">
                        {{ !empty($data->floor) ? ucfirst($data->floor) : '' }}
                    </td>
                    <td class="text-center">
                        {{ !empty($data->unit_size) ? ucfirst($data->unit_size) : '' }}
                    </td>
                    <td class="text-center">
                        {{ !empty($data->member_info->ownership_type) ? ucfirst($data->member_info->ownership_type) : ''
                        }}
                    </td>
                    <td class="text-center">
                        {{ !empty($data->member_info->name) ? ucfirst($data->member_info->name) : '' }}
                    </td>
                    <td class="text-center">
                        {{ !empty($data->member_info->phone) ? ucfirst($data->member_info->phone) : '' }}
                    </td>
                    @if (empty($data->member_info))
                    <td class="text-center exp-status-btn"><button>Vacant</button></td>
                    <td></td>
                    @else
                    <td class="text-center up-status-btn"><button>Occupied</button></td>
                    <td class="text-center">
                        <a class="view"
                            href="{{ route($thisModule . '.member.details', ['id' => $data->member_info->id]) }}"
                            id="{{ $data->member_info->id }}">
                            <i class="fa-regular fa-user" title="Member Details" style="margin-right:8px"></i>
                        </a>
                    </td>
                    @endif
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
                {{-- <div class="d-flex justify-content-between p-2 mt-2 mb-2">
                    <div>
                        Showing {{ $datas->firstItem() }} to {{ $datas->lastItem() }} of
                        {{ $datas->total() }} results
                    </div>
                    <div>
                    </div>
                </div> --}}

            </div>
        </div>
    </div>
    {{-- <div class="table-pagination">
        <a class="pre" href="#"><svg width="25" height="8" viewBox="0 0 25 8">
                <path
                    d="M0.396446 4.34391C0.201185 4.14865 0.201185 3.83207 0.396446 3.6368L3.57843 0.454823C3.77369 0.25956 4.09027 0.25956 4.28553 0.454823C4.4808 0.650085 4.4808 0.966667 4.28553 1.16193L1.45711 3.99036L4.28553 6.81878C4.4808 7.01405 4.4808 7.33063 4.28553 7.52589C4.09027 7.72115 3.77369 7.72115 3.57843 7.52589L0.396446 4.34391ZM24.75 4.49036H0.75V3.49036H24.75V4.49036Z" />
            </svg>
        </a>
        <a class="active" href="#">1</a>
        <a href="#">2</a>
        <a class="next" href="#">
            <svg width="25" height="8" viewBox="0 0 25 8">
                <path
                    d="M24.6036 4.34391C24.7988 4.14865 24.7988 3.83207 24.6036 3.6368L21.4216 0.454823C21.2263 0.25956 20.9097 0.25956 20.7145 0.454823C20.5192 0.650085 20.5192 0.966667 20.7145 1.16193L23.5429 3.99036L20.7145 6.81878C20.5192 7.01405 20.5192 7.33063 20.7145 7.52589C20.9097 7.72115 21.2263 7.72115 21.4216 7.52589L24.6036 4.34391ZM0.25 4.49036H24.25V3.49036H0.25V4.49036Z" />
            </svg>
        </a>

    </div> --}}
</div>

@endsection

@push('footer-script')
<script>
    $(".members_page").on('click', '#addNoticeModalOpen', function (event) {
        // set modal form
        $('#addNoticeForm').attr('action',
            '{{ route($thisModule . '.notice.store') }}'
        );
        $('#submitAddNoticeForm').attr('data-formtype', 'add');
        $('#modalHeadTxt').text('Add Notice');

        // reset form data
        $('#addNoticeForm').find(
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
        $('#addNoticeModal').modal({
            backdrop: 'static',
            keyboard: false
        });
        // check form1 validation and switch to next form2
        $(".modal").on('click', '#submitAddNoticeForm', async function (event) {
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
    $(document).ready(function () {
        $('body').on('click', '.edit', function () {
            $('.err').text('');
            // loader add
            $('#loader').css('width', '50%');
            $('#loader').fadeIn();
            $('#blockOverlay').fadeIn();

            // $('#addNoticeForm')[0].reset();
            $('#addNoticeForm').find(
                'input:not([name="_token"],[name^="unit_type"],[name^="created_by"]), select, textarea'
            ).each(
                function () {
                    $(this).val('');
                });

            $('#addNoticeForm select').each(function () {
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
                success: function (res) {
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
<script>
    // Wait for document to be ready
    $(document).ready(function () {
        // Submit handler for the form
        $('#searchForm').submit(function (event) {
            // Clear previous error messages
            $('.err').text('');

            // Get the values of checkin_date and checkout_date
            var tower = $('#tower').val();
            var floor = $('#floor').val();

            // Validation check: If one date is filled and the other is not
            if ((floor && !tower)) {
                // Prevent form submission
                event.preventDefault();

                // Show error message in the corresponding input's error span
                if (!tower) {
                    $('#tower').text('Required.');
                }
            }
        });
    });
</script>
<script>
    $(document).ready(function () {
        $('#tower').on('change', function () {
            var block_name = $(this).val(); // Get selected block name

            if (block_name) {
                $.ajax({
                    url: "{{ route($thisModule . '.society.getBlockFloor') }}",
                    type: "GET",
                    data: { block_name: block_name },
                    success: function (response) {
                        if (response.status) {
                            var floors = response.data.blocks[0].floors;
                            $('#floor').empty().append('<option value="">--Select--</option>');

                            if (floors) {
                                $.each(floors, function (key, floor) {
                                    $('#floor').append('<option value="' + floor.name + '">' + floor.name + '</option>');
                                });
                            }
                        } else {
                            $('#floor').empty().append('<option value="">No Floors Found</option>');
                        }
                    },
                    error: function () {
                        $('#floor').empty().append('<option value="">Error fetching data</option>');
                    }
                });
            } else {
                $('#floor').empty().append('<option value="">--Select--</option>');
            }
        });
    });
</script>
@endpush