// Toggler sidebar js
jQuery(document).on('click', '.togger_sidebar', function(){
    jQuery('.side_header').toggleClass('open');
})

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
