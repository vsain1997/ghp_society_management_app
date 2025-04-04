function manageAddEditProcess(e){
    let route = $(e).data('target');
    let modal = $(e).data('modal');
    editModal = $('#'+modal);

    console.log('modal', modal);

    $.get(route, function(h) {

        editModal.modal('show');
        editModal.find('#modal-content-body').html(h);
        initSelect2InModal(modal);
    }, 'html')
    .fail(err => {
        console.table(err);
    })
    .always(() => {
        console.log('refreshed');
    });
}



function initSelect2InModal(modalId) {
    $('#' + modalId).find('.select2').select2({
        dropdownParent: $('#' + modalId)
    });
}




function submitForm(e, formId, modalId='', title = 'You want to update this?', refresh = false, reload = false) {
    event.preventDefault();
    if(modalId != ''){
        var formCheck = $('#'+modalId).find('form#' + formId).get(0);
        var form = $('#'+modalId).find('form#' + formId);
    }else{
        var formCheck = $('form#' + formId).get(0);
        var form = $('form#' + formId);
    }

    if (!formCheck.checkValidity()) {
        formCheck.reportValidity();
        return;
    }

    var data = new FormData(form.get(0));
    var url = form.prop('action');


    Swal.fire({
        title: 'Are you sure?',
        text: title,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: "Yes, Am Sure",
    }).then((result) => {
        if(result.isConfirmed){
            if(modalId != ''){
                var processing = $('#'+modalId).find('#processing');
                var formDiv = $('#'+modalId).find('#form_div');
                //processing.show();
                //formDiv.hide();
            }

            $.ajax({
                url: url,
                type: "POST",
                data: data,
                processData: false,
                contentType: false,
                success: function(response) {
                    if(response.status == 'success'){
                        console.log(response.message);
                        form.trigger('reset');
                        removeErrorTexts(form);

                        if(refresh == true){
                            refreshContent();
                        }

                        if(reload == true){
                            window.location.reload();
                        }

                        if(modalId != ''){
                            processing.hide();
                            $('#'+modalId).modal('hide');
                        }
                        toastr[response.status](response.message);
                    }else{
                        form.trigger('reset');
                        if (response.status == 'error') {
                            toastr[response.status](response.message);
                        }
                        removeErrorTexts(form);
                    }
                },
                error: function(err) {
                    if(err.responseJSON.errors){
                        console.log(err);
                        addErrorTexts($(form), err.responseJSON.errors, true);
                        processing.hide();
                        formDiv.show();
                    }
                }
            })
            .fail(err => {
                processing.hide();
                formDiv.show();
            })
            .always(()=>{
                processing.hide();
                formDiv.show();
            });
        }else{
        }
    })
}
