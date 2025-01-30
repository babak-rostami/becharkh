$(document).ready(function() {
    // Check if the 'page_seen' cookie is set for the current path
    if (getCookie("page_seen") == null) {
        // Set the 'page_seen' cookie for the current path with an expiration time of 1 hour
        setCookie("page_seen", "1", 3600, window.location.pathname);
    }
});

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
    changeLikeAndUnlike(is_like, is_unlike);
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

$(".comment-span").click(function(e) {
    $("html, body").animate(
        {
            scrollTop: $("#commentsSection").offset().top
        },
        500
    );
});

// if is_like is like else unlike
function likeBlog(is_like) {
    $.ajax({
        type: "POST",
        url: blog_like_route,
        data: {
            _token: blog_show_csrf,
            like_or_unlike: is_like,
            blog_id: blog_id
        },
        success: function(data) {
            if (data.status == 0) {
                //delete like
                changeLikeAndUnlike(0, 0);
            } else if (data.status == 1) {
                //like and delete unlike
                changeLikeAndUnlike(1, 0);
            } else if (data.status == 2) {
                // delete unlike
                changeLikeAndUnlike(0, 0);
            } else if (data.status == 3) {
                // unlike and delete like
                changeLikeAndUnlike(0, 1);
            }
            changeLikeAndUnlikeCount(data.like_count, data.unlike_count);
        }
    });
}

function changeLikeAndUnlikeCount(likeCount, unlikeCount) {
    $("#like-count-span").text(likeCount);
    $("#unlike-count-span").text(unlikeCount);
}

function changeLikeAndUnlike(islike, isUnlike) {
    is_like = parseInt(islike);
    is_unlike = parseInt(isUnlike);

    if ($("img#unlike-image").length === 0) {
        var $likeImg = $("<img>", {
            id: "unlike-image",
            alt: "unlike"
        });
        $("#unlike-btn").append($likeImg);
    }
    if ($("img#like-image").length === 0) {
        var $likeImg = $("<img>", {
            id: "like-image",
            alt: "like"
        });
        $("#like-btn").append($likeImg);
    }
    if (is_like) {
        $("#like-image").attr("src", like_img);
    } else {
        $("#like-image").attr("src", no_like_img);
    }
    if (is_unlike) {
        $("#unlike-image").attr("src", unlike_img);
    } else {
        $("#unlike-image").attr("src", no_unlike_img);
    }
}

// $("#like-btn").click(function(e) {
//     e.preventDefault();
//     $("#like-btn").css("display", "none");
//     $.ajax({
//         type: "GET",
//         url: blog_like_route,
//         data: {
//             blog_id: blog_id, // < note use of 'this' here
//             like_or_unlike: $("#like-span").text()
//         },
//         success: function(result) {
//             if (result.is_like == "لایک") {
//                 $("#like-span").text("لایک شد");
//                 $("#like-btn").removeClass("btn-outline-danger");
//                 $("#like-btn").addClass("btn-outline-light");
//                 $("#like-image").attr("src", red_like_img);
//             } else {
//                 $("#like-span").text("لایک");
//                 $("#like-btn").removeClass("btn-outline-light");
//                 $("#like-btn").addClass("btn-outline-danger");
//                 $("#like-image").attr("src", white_like_img);
//             }
//             $("#like-count-span").text(result.likes_count);
//         },
//         complete: function() {
//             $("#like-btn").css("display", "inline-block");
//         }
//     });
// });

var d = 0;
setInterval(function() {
    if (d == 0) {
        $(".click-go-question").css("background-color", "#48C9B0");
        d = 1;
    } else {
        $(".click-go-question").css("background-color", "#f1f1f1");
        d = 0;
    }
}, 500);

function likeBlogComment(blog_comment_id) {
    $.ajax({
        type: "POST",
        url: blog_comment_like_route,
        data: {
            _token: blog_show_csrf,
            like_or_unlike: true,
            blog_comment_id: blog_comment_id
        },
        success: function(data) {
            var ellike = "blog-comment-like-count-" + blog_comment_id;
            document.getElementById(ellike).innerHTML = data.likecount;

            var elunlike = "blog-comment-unlike-count-" + blog_comment_id;
            document.getElementById(elunlike).innerHTML = data.unlikecount;
        }
    });
}

function unlikeBlogComment(blog_comment_id) {
    $.ajax({
        type: "POST",
        url: blog_comment_like_route,
        data: {
            _token: blog_show_csrf,
            like_or_unlike: false,
            blog_comment_id: blog_comment_id
        },
        success: function(data) {
            var ellike = "blog-comment-like-count-" + blog_comment_id;
            document.getElementById(ellike).innerHTML = data.likecount;

            var elunlike = "blog-comment-unlike-count-" + blog_comment_id;
            document.getElementById(elunlike).innerHTML = data.unlikecount;
        }
    });
}

//for send comment after auth
function setActionForAfterAuth(action = null, action_id = null) {
    if (action == null) {
        send_comment_after_login = 0;
    }
    if (action == "comment") {
        send_comment_after_login = action_id;
    }
}
function doThisAfterAuth() {
    if (send_comment_after_login != 0) {
        formObj = $(`#${send_comment_after_login}`);
        formObj.submit();
    } else {
        location.reload();
    }
}
//end for send comment after auth

// for go to comment section btn
$.fn.inViewport = function() {
    var elementTop = this.offset().top + 70;
    var elementBottom = elementTop + this.outerHeight();
    var viewportTop = $(window).scrollTop();
    var viewportBottom = viewportTop + $(window).height();

    return elementBottom > viewportTop && elementTop < viewportBottom;
};
$(window).on("resize scroll", function() {
    if ($("#blog_comment_form").inViewport()) {
        $("#goToCommentForm").hide();
        $("#goToCommentFormDesk").hide();
    } else {
        $("#goToCommentForm").show();
        $("#goToCommentFormDesk").show();
    }
});
$("#goToCommentForm").click(function() {
    $("html, body").animate(
        {
            scrollTop: $("#blog_comment_form").offset().top - 120
        },
        500
    );
});
$("#goToCommentFormDesk").click(function() {
    $("html, body").animate(
        {
            scrollTop: $("#blog_comment_form").offset().top - 120
        },
        500
    );
});
//end for go to comment section btn

/* for bslider */
function createSlider(
    sliderElement,
    itemClass,
    no_scroll = 0,
    scroll_time = 4000,
    scroll_smooth = 0
) {
    let isDown = false;
    let startX;
    let scrollLeft;
    let selected_a;
    let velX = 0;
    let momentumID;
    let autoScrolling = true;
    let autoScrollingDir = true;

    sliderElement.addEventListener("mousedown", e => {
        if (e.button === 0) {
            // only trigger on left clicks
            isDown = true;
            sliderElement.classList.add("bslider-active");
            startX = e.pageX - sliderElement.offsetLeft;
            scrollLeft = sliderElement.scrollLeft;
            cancelMomentumTracking();
            let anchor = e.target.closest(`a`);
            if (anchor) {
                selected_a = anchor.id;
            }
        }
    });

    sliderElement.addEventListener("mouseup", () => {
        isDown = false;
        sliderElement.classList.remove("bslider-active");
        beginMomentumTracking();
        if (selected_a != null) {
            setTimeout(() => {
                document
                    .getElementById(selected_a)
                    .dispatchEvent(new MouseEvent("click", { bubbles: true }));
                selected_a = null;
            }, 50);
        }
    });

    sliderElement.addEventListener("click", event => {
        event.preventDefault();
    });

    sliderElement.addEventListener("mousemove", e => {
        autoScrolling = false;
        if (!isDown) return;
        selected_a = null;
        const x = e.pageX - sliderElement.offsetLeft;
        const walk = (x - startX) * 0.75;
        var prevScrollLeft = sliderElement.scrollLeft;
        sliderElement.scrollLeft = scrollLeft - walk;
        velX = sliderElement.scrollLeft - prevScrollLeft;
    });

    sliderElement.addEventListener("mouseleave", () => {
        autoScrolling = true;
        isDown = false;
        sliderElement.classList.remove("bslider-active");
    });

    sliderElement.addEventListener("wheel", e => {
        cancelMomentumTracking();
    });

    function beginMomentumTracking() {
        cancelMomentumTracking();
        momentumID = requestAnimationFrame(momentumLoop);
    }

    function cancelMomentumTracking() {
        cancelAnimationFrame(momentumID);
    }

    function momentumLoop() {
        sliderElement.scrollLeft += velX;
        velX *= 0.95;
        if (scroll_smooth) {
            momentumID = requestAnimationFrame(momentumLoop);
        } else {
            if (Math.abs(velX) > 0.5) {
                momentumID = requestAnimationFrame(momentumLoop);
            }
        }
    }

    if (!no_scroll) {
        setInterval(() => {
            if (autoScrolling) {
                if (!autoScrollingDir) {
                    if (sliderElement.scrollLeft >= 0) {
                        autoScrollingDir = true;
                    } else {
                        sliderElement.scrollLeft += 5;
                        velX = 5;
                    }
                } else {
                    if (scroll_smooth) {
                        if (
                            sliderElement.scrollLeft - 1 <=
                            -(
                                sliderElement.scrollWidth -
                                sliderElement.offsetWidth
                            )
                        ) {
                            sliderElement.scrollLeft = 0;
                        }
                        velX = -0.1;
                    } else {
                        if (
                            sliderElement.scrollLeft - 1 <=
                            -(
                                sliderElement.scrollWidth -
                                sliderElement.offsetWidth
                            )
                        ) {
                            autoScrollingDir = false;
                        } else {
                            sliderElement.scrollLeft -= 5;
                            velX = -5;
                        }
                    }
                }
                beginMomentumTracking();
            }
        }, scroll_time);
    }
}

// const videoSlider = document.getElementById("video-slider");
// createSlider(videoSlider, "video-slider-item");

/* end for bslider */
