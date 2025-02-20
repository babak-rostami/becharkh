$(document).ready(function () {
    // Check if the 'page_seen' cookie is set for the current path
    if (getCookie("page_seen") == null) {
        // Set the 'page_seen' cookie for the current path with an expiration time of 1 hour
        setCookie("page_seen", "1", 3600, window.location.pathname);
    }
});

$(document).ready(function () {
    $(document).on("click", function (event) {
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

$("#choose-f-btn").click(function () {
    $("#choose-format-box").slideToggle();
});

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

$(".comment-span").click(function (e) {
    $("html, body").animate(
        {
            scrollTop: $("#commentsSection").offset().top
        },
        500
    );
});

// if is_like is like else unlike
function likeVideo(is_like) {
    $.ajax({
        type: "POST",
        url: video_like_route,
        data: {
            _token: video_show_csrf,
            like_or_unlike: is_like,
            video_id: video_id
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

function likeVideoComment(video_comment_id) {
    $.ajax({
        type: "POST",
        url: video_comment_like_route,
        data: {
            _token: video_show_csrf,
            like_or_unlike: true,
            video_comment_id: video_comment_id
        },
        success: function (data) {
            var ellike = "video-comment-like-count-" + video_comment_id;
            document.getElementById(ellike).innerHTML = data.likecount;

            var elunlike = "video-comment-unlike-count-" + video_comment_id;
            document.getElementById(elunlike).innerHTML = data.unlikecount;
        }
    });
}

function unlikeVideoComment(video_comment_id) {
    $.ajax({
        type: "POST",
        url: video_comment_like_route,
        data: {
            _token: video_show_csrf,
            like_or_unlike: false,
            video_comment_id: video_comment_id
        },
        success: function (data) {
            var ellike = "video-comment-like-count-" + video_comment_id;
            document.getElementById(ellike).innerHTML = data.likecount;

            var elunlike = "video-comment-unlike-count-" + video_comment_id;
            document.getElementById(elunlike).innerHTML = data.unlikecount;
        }
    });
}

function countCharacters(input, min = null, max = null) {
    var currentLength = $(input).val().length;
    var maxLength = max;
    var checkMax = 0;
    var minLength = min;
    var charMinSpanId = "#charCountMin-" + input.id;
    var CharMinSpan = $(charMinSpanId);
    var charMaxSpanId = "#charCountMax-" + input.id;
    var CharMaxSpan = $(charMaxSpanId);

    $(".error").hide();
    $(".body-error").hide();

    if (minLength != null) {
        var remainingCharMin = minLength - currentLength;
        if (remainingCharMin < 0) {
            remainingCharMin = 0;
        }
        if (currentLength >= minLength) {
            CharMinSpan.hide();
            checkMax = 1;
        } else {
            checkMax = 0;
            CharMinSpan.show();
            CharMaxSpan.hide();
            CharMinSpan.text(
                "حداقل " + remainingCharMin + " کاراکتر دیگر وارد کنید"
            );
        }
    }
    if (maxLength != null && checkMax == 1) {
        CharMaxSpan.show();
        var remainingCharMax = maxLength - currentLength;
        if (remainingCharMax < 0) {
            remainingCharMax = 0;
        }
        if (currentLength > maxLength) {
            $(input).val(
                $(input)
                    .val()
                    .substring(0, maxLength)
            ); // Truncate the input value to the maximum limit
        }
        CharMaxSpan.text("تعداد کاراکتر باقی مانده : " + remainingCharMax);
    }
}

function saveComment(id = null) {
    // Prevent the default action (submitting the form or refreshing the page)
    $(".input-char-min").hide();
    var count = 0;
    if (id == null) {
        saveButton = $("#saveVideoCommentBtn");
        //validate body
        if ($("#body").val().length < 10) {
            $("#body-error").show();
            $("#body-error").text("حداقل 10 کلمه وارد کنید");
        } else {
            count++;
        }
    } else {
        saveButton = $(`#saveCommentbtn-` + id);
        //validate body
        if ($(`#body-` + id).val().length < 10) {
            $(`#body-` + id + `-error`).show();
            $(`#body-` + id + `-error`).text("حداقل 10 کلمه وارد کنید");
        } else {
            count++;
        }
    }

    if (count == 1) {
        saveButton.prop("disabled", true);
        saveButton.html(loadingGif);
        saveButton.attr("class", "btn btn-light bt-2 w-100");
        if (id == null) {
            $("#videoCommentForm").submit();
        } else {
            $(`#replyForm-` + id).submit();
        }
    }
}
