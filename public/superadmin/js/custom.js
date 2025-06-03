// Toggler sidebar js
jQuery(document).on('click', '.togger_sidebar', function(){
    jQuery('.side_header').toggleClass('open');
})

// Active or In-active status js
// jQuery(document).on('change', '.statusOption', function(){
//     let status = jQuery(this).val();
//     jQuery(this).parents('td').find('input[type=hidden]').val(status);
// });

// add socoity Tabs js
jQuery(document).on('click', '.add_society_wrapper .nav-link', function(){
    jQuery(this).parent().siblings().removeClass('active_item');
    jQuery(this).parent().addClass('active_item');
})

// jQuery(document).on('click', '.next_btn', function(){
    // jQuery('.add_society_wrapper .nav-link.active').parent().next().find('.nav-link').trigger('click');
// })

// Society add block js
jQuery(document).on('click', '.add_block_field', function(){

    // let getFlag = validate_society_blocks();
    // if(getFlag){
    //     toastr.error('Kindly check if all the forms are filled out accurately !')
    //     return 1;
    // }
    let inputedBlockNos = Number($("#totalTowers").val());
    let totalBlocksCount = jQuery('#accordionBlock').children().length;
    if(inputedBlockNos <= totalBlocksCount){
        // console.log("totalBlocksCount",totalBlocksCount,"no more blocks");
        toastr.error("You cannot add more towers. Maximum limit reached!");
        return false;
    }

    let serial = Number(jQuery('#accordionBlock').children().last().data('serial'));

    if (typeof serial === 'undefined' || serial === null || isNaN(serial)) {
        serial = 1;
    }else{
        serial = serial + 1;
    }

    let itemHTML = `
    <div class="accordion-item" data-serial="${serial}">
        <h2 class="accordion-header" id="blockHeading${serial}">
            <button class="accordion-button"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#blockCollapse${serial}"
                aria-expanded="true"
                aria-controls="blockCollapse${serial}">
                <span class="showBlockName"></span>
            </button>
        </h2>
        <div id="blockCollapse${serial}" class="accordion-collapse collapse show"
            aria-labelledby="blockHeading${serial}"
            data-bs-parent="#accordionBlock">
            <div class="accordion-body">
                <div class="block_fields">
                    <div class="custom_form">
                        <div class="form">
                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <label>Tower Name/Block Name</label>
                                        <input type="text" name="bname[${serial}]" class="form-control">
                                        <span class="text-danger err"></span>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <label>Total Units</label>
                                        <input type="text" name="totalFloors[${serial}]" class="form-control">
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
                                <tbody id="tower_property_${serial}">
                                    <tr>
                                        <td>
                                            <input type="hidden" name="block_id[${serial}][]">
                                            <input type="text"
                                                name="property_number[${serial}][]" class="block-field" style="width: 100%;">
                                            <br>
                                            <span class="text-danger err"></span>
                                        </td>
                                        <td>
                                            <input type="text"
                                                name="property_floor[${serial}][]"
                                                class="block-field">
                                            <br>
                                            <span class="text-danger err"></span>
                                        </td>
                                        <td>
                                            <select name="property_type[${serial}][]" class="block-field" id="">
                                                <option value="residential">Residential</option>
                                                <option value="commercial">Commercial</option>
                                            </select>
                                            <br>
                                            <span class="text-danger err"></span>
                                        </td>
                                        <td class="d-none">
                                            <select name="ownership[${serial}][]" class="block-field" id="">
                                                <option value="vacant">Vacant</option>
                                                <option value="occupied">Occupied</option>
                                            </select>
                                            <br>
                                            <span class="text-danger err"></span>
                                        </td>
                                        <td>
                                            <input type="text"
                                                name="unit_size[${serial}][]"
                                                class="block-field">
                                            <br>
                                            <span class="text-danger err"></span>
                                        </td>
                                        <td>
                                            <select name="bhk[${serial}][]" class="block-field" id="">
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
                                            <a href="javascript:void(0)" data-tower-number="${serial}"
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
`;

    jQuery('#accordionBlock').append(itemHTML);

});

jQuery(document).on('click', '#add_emer_row', function(){

    let sl = Number(jQuery('#emer_block').children().last().data('sl'));

    if (typeof sl === 'undefined' || sl === null || isNaN(sl)) {
        sl = 1;
    }else{
        sl = sl + 1;
    }

let emer_html = `
    <div class="row" data-sl="${sl}">
        <div class="col">
            <div class="form-group">
                <label>Name</label>
                <input type="text" name="emr_name[${sl}]" class="form-control">
                <span class="text-danger err"></span>
            </div>
        </div>
        <div class="col">
            <div class="form-group">
                <label>Designation</label>
                <input type="text" name="emr_designation[${sl}]"
                    class="form-control">
                <span class="text-danger err"></span>
            </div>
        </div>
        <div class="col">
            <div class="form-group">
                <label>Phone</label>
                <input type="text" name="emr_phone[${sl}]"
                    class="form-control phone-input-restrict">
                <span class="text-danger err"></span>
            </div>
        </div>
        <div class="col">
            <div class="form-group">
                <a href="javascript:void(0)"
                    class="btn btn-danger remove_emer_row mt-4">-
                </a>
            </div>
        </div>
    </div>`;

    jQuery('#emer_block').append(emer_html);
    jQuery(document).on('input', '.phone-input-restrict', function() {
        this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);  // Restrict to 10 digits
    });
});

// $(".sos-drop-btn").click(function(){
//     $(".sos-list").toggleClass("show-sos");
// });

$(document).ready(function () {
    $(".item-drop-btn").click(function (e) {
        e.stopPropagation(); // Prevents bubbling up

        // Toggle only the clicked menu, not all menus
        $(this).closest(".item-list").toggleClass("show-items");

        // Optional: Close other open menus
        $(".item-list").not($(this).closest(".item-list")).removeClass("show-items");
    });

    // Click anywhere outside to close all menus
    // $(document).click(function () {
    //     $(".item-list").removeClass("show-items");
    // });

    // Prevent closing when clicking inside the menu
    $(".item-list").click(function (e) {
        e.stopPropagation();
    });
});


jQuery(document).on('click', '.add_property_row', function(){



    // let sl = Number(jQuery('#emer_block').children().last().data('sl'));
    let sl = Number(jQuery(this).data('tower-number'));
    console.log('sl',sl);

    // if (typeof sl === 'undefined' || sl === null || isNaN(sl)) {
    //     sl = 1;
    // }else{
    //     sl = sl + 1;
    // }

let property_html = `
        <tr>
            <td>
                <input type="hidden" name="block_id[${sl}][]">
                <input type="text"
                    name="property_number[${sl}][]" class="block-field" style="width: 100%;">
                <br>
                <span class="text-danger err"></span>
            </td>
            <td>
                <input type="text"
                    name="property_floor[${sl}][]"
                    class="block-field">
                <br>
                <span class="text-danger err"></span>
            </td>
            <td>
                <select name="property_type[${sl}][]" class="block-field" id="">
                    <option value="residential">Residential</option>
                    <option value="commercial">Commercial</option>
                </select>
                <br>
                <span class="text-danger err"></span>
            </td>
            <td class="d-none">
                <select name="ownership[${sl}][]" class="block-field" id="">
                    <option value="vacant">Vacant</option>
                    <option value="occupied">Occupied</option>
                </select>
                <br>
                <span class="text-danger err"></span>
            </td>
            <td>
                <input type="text"
                    name="unit_size[${sl}][]"
                    class="block-field">
                <br>
                <span class="text-danger err"></span>
            </td>
            <td>
                <select name="bhk[${sl}][]" class="block-field" id="">
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
                <a href="javascript:void(0)"
                    class="btn btn-danger remove_property_row">-
                </a>
            </td>

        </tr>
        `;

    jQuery('#tower_property_'+sl).append(property_html);
    // jQuery(document).on('input', '.phone-input-restrict', function() {
    //     this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);  // Restrict to 10 digits
    // });
});

$(".remove-date").click(function(){
    $("span").removeClass("date-plac");
  });

  $(".remove-time").click(function(){
    $("span").removeClass("time-plac");
  });