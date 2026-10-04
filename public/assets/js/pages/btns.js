if (typeof goToCommentForm !== "undefined" && typeof page !== "undefined") {
    if (page == "comment" || page == "show_question") {
        // Define the inViewport function to check if an element is in the viewport
        $.fn.inViewport = function() {
            var elementTop = this.offset().top + 70; // Adjust for a margin
            var elementBottom = elementTop + this.outerHeight();
            var viewportTop = $(window).scrollTop();
            var viewportBottom = viewportTop + $(window).height();

            return elementBottom > viewportTop && elementTop < viewportBottom;
        };

        // Check visibility of the comment form on window resize or scroll
        $(window).on("resize scroll", function() {
            if ($("#cm_form").inViewport()) {
                $("#goToCommentForm").hide();
            } else {
                $("#goToCommentForm").show();
            }
        });

        // Use event delegation to handle clicks on #goToCommentForm
        $(document).on("click", "#goToCommentForm", function() {
            $("html, body").animate(
                {
                    scrollTop: $("#cm_form").offset().top - 120 // Scroll to the comment form
                },
                500
            );
        });
    }
}
