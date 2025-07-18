let tabText = $("#tab-title-box-text");
let tabTextBox = $("#tab-title-box");
let tabTextMaxWidth = 80;
// tabTextBox.hide();
const stickyElement = $("#tabs");
if (stickyElement.offset()) {
    const offsetTop = stickyElement.offset().top;
    $(window).scroll(function () {
        if ($(window).scrollTop() >= offsetTop) {
            stickyElement.addClass("sticky-tab");
            // tabTextBox.show();
            $("#top-menu-box").addClass("d-none");
        } else {
            stickyElement.removeClass("sticky-tab");
            // tabTextBox.hide();
            $("#top-menu-box").removeClass("d-none");
        }
    });

    if (tabText.width() >= tabTextMaxWidth) {
        $("#tab-title-box-text").addClass("tab-scroll-text");
    }
}
