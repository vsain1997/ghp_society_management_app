<div class="head_content">
    <div class="left_head">
        <h2>Society Management</h2>
        <p>Add or manage societies</p>
    </div>
    <!-- Button trigger modal -->
    <button type="button" class="bg_theme_btn" data-bs-toggle="modal" data-bs-target="#addSocietyModal">
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
        <div class="search_wrapper">
            <form>
                <div class="input-group">
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
                    <input type="search" name="search" id="search" placeholder="Search">
                </div>
            </form>
        </div>
        <div class="right_filters">
            <button class="sortby">
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
                    <th>ID</th>
                    <th>Name</th>
                    <th>Location</th>
                    <th>Total Units</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>01</td>
                    <td>CHP Smart Society</td>
                    <td>789 Pinecrest Avenue, Pinecrest Heights, TX 23456</td>
                    <td>200</td>
                    <td>
                        <input type="hidden" name="statusVal" value="1">
                        <div class="status_select">
                            <select name="status" class="statusOption form-select">
                                <option value="1">Active</option>
                                <option value="0">In Active</option>
                            </select>
                        </div>
                    </td>
                    <td>
                        <div class="actions">
                            <a href="#">
                                <img src="img/edit.png" alt="edit">
                            </a>
                            <a href="#">
                                <img src="img/eye.png" alt="eye">
                            </a>
                            <a href="#">
                                <img src="img/delete.png" alt="delete">
                            </a>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>01</td>
                    <td>CHP Smart Society</td>
                    <td>789 Pinecrest Avenue, Pinecrest Heights, TX 23456</td>
                    <td>200</td>
                    <td>
                        <input type="hidden" name="statusVal" value="1">
                        <div class="status_select">
                            <select name="status" class="statusOption form-select">
                                <option value="1">Active</option>
                                <option value="0">In Active</option>
                            </select>
                        </div>
                    </td>
                    <td>
                        <div class="actions">
                            <a href="#">
                                <img src="img/edit.png" alt="edit">
                            </a>
                            <a href="#">
                                <img src="img/eye.png" alt="eye">
                            </a>
                            <a href="#">
                                <img src="img/delete.png" alt="delete">
                            </a>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>01</td>
                    <td>CHP Smart Society</td>
                    <td>789 Pinecrest Avenue, Pinecrest Heights, TX 23456</td>
                    <td>200</td>
                    <td>
                        <input type="hidden" name="statusVal" value="0">
                        <div class="status_select">
                            <select name="status" class="statusOption form-select">
                                <option value="1">Active</option>
                                <option value="0" selected>In Active</option>
                            </select>
                        </div>
                    </td>
                    <td>
                        <div class="actions">
                            <a href="#">
                                <img src="img/edit.png" alt="edit">
                            </a>
                            <a href="#">
                                <img src="img/eye.png" alt="eye">
                            </a>
                            <a href="#">
                                <img src="img/delete.png" alt="delete">
                            </a>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>01</td>
                    <td>CHP Smart Society</td>
                    <td>789 Pinecrest Avenue, Pinecrest Heights, TX 23456</td>
                    <td>200</td>
                    <td>
                        <input type="hidden" name="statusVal" value="0">
                        <div class="status_select">
                            <select name="status" class="statusOption form-select">
                                <option value="1">Active</option>
                                <option value="0" selected>In Active</option>
                            </select>
                        </div>
                    </td>
                    <td>
                        <div class="actions">
                            <a href="#">
                                <img src="img/edit.png" alt="edit">
                            </a>
                            <a href="#">
                                <img src="img/eye.png" alt="eye">
                            </a>
                            <a href="#">
                                <img src="img/delete.png" alt="delete">
                            </a>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>01</td>
                    <td>CHP Smart Society</td>
                    <td>789 Pinecrest Avenue, Pinecrest Heights, TX 23456</td>
                    <td>200</td>
                    <td>
                        <input type="hidden" name="statusVal" value="1">
                        <div class="status_select">
                            <select name="status" class="statusOption form-select">
                                <option value="1">Active</option>
                                <option value="0">In Active</option>
                            </select>
                        </div>
                    </td>
                    <td>
                        <div class="actions">
                            <a href="#">
                                <img src="img/edit.png" alt="edit">
                            </a>
                            <a href="#">
                                <img src="img/eye.png" alt="eye">
                            </a>
                            <a href="#">
                                <img src="img/delete.png" alt="delete">
                            </a>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>01</td>
                    <td>CHP Smart Society</td>
                    <td>789 Pinecrest Avenue, Pinecrest Heights, TX 23456</td>
                    <td>200</td>
                    <td>
                        <input type="hidden" name="statusVal" value="1">
                        <div class="status_select">
                            <select name="status" class="statusOption form-select">
                                <option value="1">Active</option>
                                <option value="0">In Active</option>
                            </select>
                        </div>
                    </td>
                    <td>
                        <div class="actions">
                            <a href="#">
                                <img src="img/edit.png" alt="edit">
                            </a>
                            <a href="#">
                                <img src="img/eye.png" alt="eye">
                            </a>
                            <a href="#">
                                <img src="img/delete.png" alt="delete">
                            </a>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>01</td>
                    <td>CHP Smart Society</td>
                    <td>789 Pinecrest Avenue, Pinecrest Heights, TX 23456</td>
                    <td>200</td>
                    <td>
                        <input type="hidden" name="statusVal" value="0">
                        <div class="status_select">
                            <select name="status" class="statusOption form-select">
                                <option value="1">Active</option>
                                <option value="0" selected>In Active</option>
                            </select>
                        </div>
                    </td>
                    <td>
                        <div class="actions">
                            <a href="#">
                                <img src="img/edit.png" alt="edit">
                            </a>
                            <a href="#">
                                <img src="img/eye.png" alt="eye">
                            </a>
                            <a href="#">
                                <img src="img/delete.png" alt="delete">
                            </a>
                        </div>
                    </td>
                </tr>

            </tbody>
        </table>
        <div class="table_bottom_box">

        </div>
    </div>
</div>


<!-- Modal -->
<div class="modal fade custom_Modal" id="addSocietyModal" tabindex="-1" aria-labelledby="addSocietyModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content ">
            <div class="modal-header">
                <h3>Add Society</h3>
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
                    <form action="">
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="societyTab1" role="tabpanel"
                                aria-labelledby="societyTab1-tab">
                                <div class="custom_form">

                                    <div class="row">
                                        <div class="col">
                                            <div class="input-group">
                                                <label for="sname">Society Name</label>
                                                <input type="text" name="sname" id="sname"
                                                    class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <div class="input-group">
                                                <label for="location">Location</label>
                                                <input type="text" name="location" id="location"
                                                    class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <div class="input-group">
                                                <label for="statusSelect">Status</label>
                                                <select name="statusSelect" id="statusSelect"
                                                    class="form-select form-control ">
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
                                                <label for="floors">Number Of Floors</label>
                                                <input type="text" name="floors" id="floors"
                                                    class="form-control">
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="input-group">
                                                <label for="floors2">Units Per Floor</label>
                                                <input type="text" name="floors2" id="floors2"
                                                    class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <div class="input-group">
                                                <label for="assignAdmin">Assign Admin</label>
                                                <select name="assignAdmin" id="assignAdmin"
                                                    class="form-select form-control ">
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
                            <div class="tab-pane fade" id="societyTab2" role="tabpanel"
                                aria-labelledby="societyTab2-tab">
                                <div class="block_wrapper">
                                    <div class="accordion" id="accordionBlock">
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="blockHeading1">
                                                <button class="accordion-button" type="button"
                                                    data-bs-toggle="collapse" data-bs-target="#blockCollapse1"
                                                    aria-expanded="true" aria-controls="blockCollapse1">
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
                                                                        <input type="text" name="bname"
                                                                            id="bname" class="form-control">
                                                                    </div>
                                                                </div>
                                                                <div class="col">
                                                                    <div class="input-group">
                                                                        <label for="totalUnits">Total Units</label>
                                                                        <input type="text" name="totalUnits"
                                                                            id="totalUnits" class="form-control">
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
        </div>
    </div>
</div>
