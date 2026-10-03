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

