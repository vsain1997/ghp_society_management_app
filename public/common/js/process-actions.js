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
