/* for bslider */
const catSlider = document.getElementById("cat-slider");
createSlider(catSlider, "cat-slider-item");

$(document).ready(function() {
    if (getCookie("page_seen") == null) {
        setCookie("page_seen", "1", 3600, window.location.pathname);
    }
});

let slideIndex = 1;
showSlides(slideIndex);

function plusSlides(n) {
    showSlides((slideIndex += n));
}

function currentSlide(n) {
    showSlides((slideIndex = n));
}

function showSlides(n) {
    let i;
    let slides = document.getElementsByClassName("imgslider-ImageSlides");
    if (slides.length >= 1) {
        let dots = document.getElementsByClassName("imgslider-dot");
        if (n > slides.length) {
            slideIndex = 1;
        }
        if (n < 1) {
            slideIndex = slides.length;
        }
        for (i = 0; i < slides.length; i++) {
            slides[i].style.display = "none";
        }
        for (i = 0; i < dots.length; i++) {
            dots[i].className = dots[i].className.replace(
                " imgslider-active",
                ""
            );
        }
        slides[slideIndex - 1].style.display = "block";
        dots[slideIndex - 1].className += " imgslider-active";
    }
}

function clickImg(imgId) {
    open_image = imgId;
    setPrevImageId(open_image);
    setNextImageId(open_image);
    var modal = $("#imageModal");
    var img = $(`#slideImg-${imgId}`);
    var modalImg = $("#slide-img");
    if (modal.css("display") == "none") {
        modal.css("display", "flex");
    }
    modalImg.attr("src", img.attr("src"));
    var closeImage = $("#close-slide-img");
    closeImage.click(function(event) {
        event.stopPropagation();
        modal.css("display", "none");
    });

    // Add event listener to modal to close on outside click
    modal.click(function(event) {
        if (
            !$(event.target).closest(
                "#slide-img, .imgslider-prev, .imgslider-next"
            ).length
        ) {
            modal.css("display", "none");
        }
    });
}

function setPrevImageId(open_image_id) {
    if (has_video != "0") {
        if (open_image_id == 2) {
            prev_slide_image = parseInt(slide_count);
        } else {
            prev_slide_image = open_image_id - 1;
        }
    } else {
        if (open_image_id == 1) {
            prev_slide_image = parseInt(slide_count);
        } else {
            prev_slide_image = open_image_id - 1;
        }
    }
}

function setNextImageId(open_image_id) {
    if (has_video != "0") {
        if (open_image_id == slide_count) {
            next_slide_image = 2;
        } else {
            next_slide_image = open_image_id + 1;
        }
    } else {
        if (open_image_id == slide_count) {
            next_slide_image = 1;
        } else {
            next_slide_image = open_image_id + 1;
        }
    }
}

$(document).ready(function() {
    $(document).on("click", function(event) {
        if (
            $(".share-box").hasClass("hide-share") &&
            $(event.target).closest(".share-span").length
        ) {
            $(".share-box").removeClass("hide-share");
        } else if (
            !$(event.target).closest(".share-box").length ||
            $(event.target).closest(".share-span").length
        ) {
            $(".share-box").addClass("hide-share");
        }
    });
});

function copyToClipboard() {
    document.getElementById("page-url-for-clipboard").select();
    document.execCommand("copy");
    $("#copy-clipboard-done-span").css("display", "block");
    setTimeout(() => {
        $("#copy-clipboard-done-span").css("display", "none");
    }, 2000);
}

function sentPageToTelegram() {
    var shareUrl =
        "https://t.me/share/url?url=" +
        encodeURIComponent(window.location.href) +
        "&text=" +
        $("#page-title").text();
    window.open(shareUrl, "_blank");
}
function sentPageToWhatsapp() {
    var shareUrl =
        "https://api.whatsapp.com/send?text=" +
        encodeURIComponent(window.location.href);
    window.open(shareUrl, "_blank");
}
