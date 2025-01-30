$(document).ready(function() {
    // attached click event handler
    $("#gotoad").click(function() {
        $("html, body").animate(
            {
                scrollTop: $("#adsSection").offset().top
            },
            "slow"
        );
    });
});

/* for bslider */
const catSlider = document.getElementById("cat-slider");
createSlider(catSlider, "cat-slider-item");

// const videoSlider = document.getElementById("video-slider");
// createSlider(videoSlider, "video-slider-item");

/* end for bslider */

let lmk_btn = $("#lmk-btn");

function letMeKnow(category_id, item_id) {
    lmk_btn.text("لطفا منتظر بمانید...");
    lmk_btn.removeAttr("onclick");
    $.ajax({
        type: "POST",
        url: let_me_know_route,
        data: {
            category_id: category_id,
            item_id: item_id,
            _token: csrf_t
        },
        success: function(data) {
            lmk_btn.removeClass("btn-dark").addClass("btn-success");
            lmk_btn.text(data.message);
        }
    });
}
