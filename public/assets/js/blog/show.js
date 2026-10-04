$(document).ready(function () {
    // Check if the 'page_seen' cookie is set for the current path
    if (getCookie("page_seen") == null) {
        // Set the 'page_seen' cookie for the current path with an expiration time of 1 hour
        setCookie("page_seen", "1", 3600, window.location.pathname);
    }
});

$(".comment-span").click(function (e) {
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
        success: function (data) {
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

function likeBlogComment(blog_comment_id) {
    $.ajax({
        type: "POST",
        url: '/blog-comment-like',
        data: {
            _token: blog_show_csrf,
            like_or_unlike: true,
            blog_comment_id: blog_comment_id
        },
        success: function (data) {
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
        url: '/blog-comment-like',
        data: {
            _token: blog_show_csrf,
            like_or_unlike: false,
            blog_comment_id: blog_comment_id
        },
        success: function (data) {
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
// $.fn.inViewport = function() {
//     var elementTop = this.offset().top + 70;
//     var elementBottom = elementTop + this.outerHeight();
//     var viewportTop = $(window).scrollTop();
//     var viewportBottom = viewportTop + $(window).height();

//     return elementBottom > viewportTop && elementTop < viewportBottom;
// };
// $(window).on("resize scroll", function() {
//     if ($("#blog_comment_form").inViewport()) {
//         $("#goToCommentForm").hide();
//         $("#goToCommentFormDesk").hide();
//     } else {
//         $("#goToCommentForm").show();
//         $("#goToCommentFormDesk").show();
//     }
// });
// $("#goToCommentForm").click(function() {
//     $("html, body").animate(
//         {
//             scrollTop: $("#blog_comment_form").offset().top - 120
//         },
//         500
//     );
// });
// $("#goToCommentFormDesk").click(function() {
//     $("html, body").animate(
//         {
//             scrollTop: $("#blog_comment_form").offset().top - 120
//         },
//         500
//     );
// });
//end for go to comment section btn

/* for bslider */
const catSlider = document.getElementById("cat-slider");
createSlider(catSlider, "cat-slider-item");
