@extends($thisModule . '::layouts.master')

@section('title', 'Society')

@section('content')
    <div class="right_main_body_content members_page">
        <div class="head_content">
            <div class="left_head">
                <h2>Society</h2>
                <p>Society Details</p>
            </div>
            <!-- Button trigger modal -->
            {{-- <button type="button" class="bg_theme_btn" data-bs-toggle="modal" data-bs-target="#addMemberModal">
                <svg width="14" height="15" viewBox="0 0 14 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M13.0005 8.5H8.00049V13.5C8.00049 14.05 7.55049 14.5 7.00049 14.5C6.45049 14.5 6.00049 14.05 6.00049 13.5V8.5H1.00049C0.450488 8.5 0.000488281 8.05 0.000488281 7.5C0.000488281 6.95 0.450488 6.5 1.00049 6.5H6.00049V1.5C6.00049 0.95 6.45049 0.5 7.00049 0.5C7.55049 0.5 8.00049 0.95 8.00049 1.5V6.5H13.0005C13.5505 6.5 14.0005 6.95 14.0005 7.5C14.0005 8.05 13.5505 8.5 13.0005 8.5Z"
                        fill="white"></path>
                </svg>
                Add New Member
            </button> --}}
        </div>
        <div class="add_society_wrapper">

            <form action="">
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="societyTab1" role="tabpanel" aria-labelledby="societyTab1-tab">
                        <div class="custom_form">

                            <div class="row">
                                <div class="col">
                                    <div class="input-group">
                                        <label for="sname">Society Name</label>
                                        <input type="text" name="sname" id="sname" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="input-group">
                                        <label for="location">Location</label>
                                        <input type="text" name="location" id="location" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="input-group">
                                        <label for="statusSelect">Status</label>
                                        <select name="statusSelect" id="statusSelect" class="form-select form-control ">
                                            <option value="" selected>--select--</option>
                                            <option value="1">Active</option>
                                            <option value="2">In active</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="input-group">
                                        <label for="floors">Number Of Floors </label>
                                        <input type="text" name="floors" id="floors" class="form-control">
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="input-group">
                                        <label for="floors2">Units Per Floor </label>
                                        <input type="text" name="floors2" id="floors2" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="input-group">
                                        <label for="assignAdmin">Assign Admin</label>
                                        <select name="assignAdmin" id="assignAdmin" class="form-select form-control ">
                                            <option value="" selected>--select--</option>
                                            <option value="1">Resident</option>
                                            <option value="2">Admin</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col text-end">
                                    <button type="button" class="border_theme_btn cancel_btn"
                                        data-bs-dismiss="modal">Cancel</button>
                                    <button type="button" class="bg_theme_btn next_btn">Next</button>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="tab-pane fade" id="societyTab2" role="tabpanel" aria-labelledby="societyTab2-tab">
                        <div class="block_wrapper">
                            <div class="accordion" id="accordionBlock">
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="blockHeading1">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#blockCollapse1" aria-expanded="true"
                                            aria-controls="blockCollapse1">
                                            Block A
                                        </button>
                                    </h2>
                                    <div id="blockCollapse1" class="accordion-collapse collapse show"
                                        aria-labelledby="blockHeading1" data-bs-parent="#accordionBlock">
                                        <div class="accordion-body">
                                            <div class="block_fields">
                                                <div class="custom_form">

                                                    <div class="row">
                                                        <div class="col">
                                                            <div class="input-group">
                                                                <label for="bname">Block Name</label>
                                                                <input type="text" name="bname" id="bname"
                                                                    class="form-control">
                                                            </div>
                                                        </div>
                                                        <div class="col">
                                                            <div class="input-group">
                                                                <label for="totalUnits">Total Units</label>
                                                                <input type="text" name="totalUnits" id="totalUnits"
                                                                    class="form-control">
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
                                                                    <th>Size</th>
                                                                    <th>QTY</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr>
                                                                    <td>Plot</td>
                                                                    <td>
                                                                        <input type="text" name="size"
                                                                            id="size" class="block-field"
                                                                            value="256 sq.ft">
                                                                    </td>
                                                                    <td>
                                                                        <input type="number" name="size"
                                                                            id="size" class="block-field"
                                                                            value="11">
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Office</td>
                                                                    <td>
                                                                        <input type="text" name="size"
                                                                            id="size" class="block-field"
                                                                            value="256 sq.ft">
                                                                    </td>
                                                                    <td>
                                                                        <input type="number" name="size"
                                                                            id="size" class="block-field"
                                                                            value="11">
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Flat</td>
                                                                    <td>
                                                                        <input type="text" name="size"
                                                                            id="size" class="block-field"
                                                                            value="256 sq.ft">
                                                                    </td>
                                                                    <td>
                                                                        <input type="number" name="size"
                                                                            id="size" class="block-field"
                                                                            value="11">
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Other</td>
                                                                    <td>
                                                                        <input type="text" name="size"
                                                                            id="size" class="block-field"
                                                                            value="256 sq.ft">
                                                                    </td>
                                                                    <td>
                                                                        <input type="number" name="size"
                                                                            id="size" class="block-field"
                                                                            value="11">
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
                                <a href="javascript:void(0)" class="add_block_field">
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
                                        <button type="button" class="bg_theme_btn">Submit</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('footer-script')
@endpush
