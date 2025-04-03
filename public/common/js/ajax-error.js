/**
 * Remove Error class and Span Error Text from a form
 * @param {object} t Target Form DomObject
 */
function removeErrorTexts(t) {
    t.find("input, select, textarea").each(function (index, elem) {
      $(elem).removeClass("error");
      $(elem).siblings("span.input-error").remove();
      $(elem).parent().siblings("span.input-error").remove();
    });
}

/**
 * Add Errors to the fields of Form
 *
 * @param {Object} t Target Form DomObject
 * @param {Object} errors Errors Object
 * @param {Boolean} p True if has parent
 * @return {void}
 */
function addErrorTexts(t, errors, p = false){
    // console.log(errors);
    $("span.input-error").remove();
    $.each(errors, function (key, msg) {
        var elem = t.find('input[name="' + key + '"], select[name="' + key + '[]"], textarea[name="' + key + '"], select[name="' + key + '"]');
        var error = "";
        if (elem.length) {
            elem.addClass("error");
            error = '<span class="input-error text-xs text-danger">' + msg + "</span>";
            // if(p) $(error).insertAfter(elem.eq(0).parent());
            if(p) $(error).insertAfter(elem.eq(0));
            else $(error).insertAfter(elem);
        }else{

        }
    });
}
