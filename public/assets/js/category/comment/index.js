$("#comImageModal").on("click", function (event) {
    if ($(event.target).attr("id") !== "com-img") {
        closeCommentModalImage();
    }
});

function clickCommentImg(imgId) {
    var modal = $("#comImageModal");
    var img = $(`#c-img-${imgId}`);
    var modalImg = $("#com-img");
    if (modal.css("display") == "none") {
        modal.css("display", "flex");
    }
    modalImg.attr("src", img.attr("src"));
    $("#close-com-img").click(function () {
        closeCommentModalImage();
    });
}

function closeCommentModalImage() {
    $("#comImageModal").hide();
}

function setPrevImageId(open_image_id) {
    if (open_image_id == 1) {
        prev_slide_image = parseInt(slide_count);
    } else {
        prev_slide_image = open_image_id - 1;
    }
}

function setNextImageId(open_image_id) {
    if (open_image_id == slide_count) {
        next_slide_image = 1;
    } else {
        next_slide_image = open_image_id + 1;
    }
}

let ccom_like_unlike_is_processing = false; // Flag to track if a like or unlike request is in progress

function likeCategoryComment(category_comment_id) {
    if (ccom_like_unlike_is_processing) return;
    ccom_like_unlike_is_processing = true;

    let likeImage = $(`#like-com-image-${category_comment_id}`);
    likeImage.addClass('clicked');
    likeImage.one('animationend', function () {
        $(this).removeClass('clicked');
    });

    $.ajax({
        type: "POST",
        url: category_comment_like_route,
        data: {
            _token: csrf_t,
            like_or_unlike: true,
            category_comment_id: category_comment_id
        },
        success: function (data) {
            var ellike = "category-comment-like-count-" + category_comment_id;
            document.getElementById(ellike).innerHTML = data.likecount;

            var elunlike = "category-comment-unlike-count-" + category_comment_id;
            document.getElementById(elunlike).innerHTML = data.unlikecount;
        },
        complete: function () {
            ccom_like_unlike_is_processing = false;
        }
    });
}

function unlikeCategoryComment(category_comment_id) {
    if (ccom_like_unlike_is_processing) return; // Prevent further clicks if a request is in progress
    ccom_like_unlike_is_processing = true; // Set the flag to true

    let unlikeImage = $(`#unlike-com-image-${category_comment_id}`);
    unlikeImage.addClass('clicked');
    unlikeImage.one('animationend', function () {
        $(this).removeClass('clicked');
    });

    $.ajax({
        type: "POST",
        url: category_comment_like_route,
        data: {
            _token: csrf_t,
            like_or_unlike: false,
            category_comment_id: category_comment_id
        },
        success: function (data) {
            var ellike = "category-comment-like-count-" + category_comment_id;
            document.getElementById(ellike).innerHTML = data.likecount;

            var elunlike = "category-comment-unlike-count-" + category_comment_id;
            document.getElementById(elunlike).innerHTML = data.unlikecount;
        },
        complete: function () {
            ccom_like_unlike_is_processing = false;
        }
    });
}

/* for bslider */
const catSlider = document.getElementById("cat-slider");
createSlider(catSlider, "cat-slider-item");
// const videoSlider = document.getElementById("video-slider");
// createSlider(videoSlider, "video-slider-item");
/* end for bslider */

function sendCommentBtnAction(button, input_id, form_id) {
    var inputId = "#" + input_id;
    var fomrId = "#" + form_id;
    inputVal = $(inputId).val();
    formObj = $(fomrId);
    if (inputVal.trim() != "") {
        button.innerHTML = loadingGif;
        button.className = "btn btn-light";
        button.disabled = true;
        formObj.submit();
    }
}

// for send comment after auth
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

$(document).ready(function () {
    $("#com-img-input").change(function () {
        var file = this.files[0];

        const reader = new FileReader();
        reader.onload = event => {
            var img = $("<img>")
                .attr("id", "comment-img")
                .attr("src", event.target.result);
            $("#comment-img-prev-box")
                .empty()
                .append(img);
        };
        reader.readAsDataURL(file);
        $("#comment-img-prev-box").show();
    });

    // $(window).scroll(function () {
    //     var hopBoxHeight = $('#hop-box').outerHeight();
    //     if (
    //         $(window).scrollTop() + $(window).height() >=
    //         $(document).height() - 500 - hopBoxHeight
    //     ) {
    // if (dontLoadMore == 0) {
    // if (nextPageUrl) {
    //     dontLoadMore = 1;
    //     $("#loadMore").show();
    //     loadMorePosts();
    // }
    // }
    //     }
    // });
    if (!nextPageUrl) {
        $("#load-more-com-btn").hide();
    }
});

function loadMorePosts() {
    let showmorebtn = $("#load-more-com-btn");
    let showmorebox = $("#loadMore").show();
    showmorebtn.hide();
    showmorebox.show();
    $.ajax({
        url: nextPageUrl,
        type: "get",
        success: function (data) {
            nextPageUrl = data.nextPageUrl;
            $("#commentsBox").append(data.html);
            $("#loadMore").hide();
            dontLoadMore = 0;
            scrollToId(data.scrollTo);
            showmorebox.hide();
            if (nextPageUrl) {
                showmorebtn.show();
            }
        },
        error: function (xhr, status, error) {
            dontLoadMore = 0;
            console.error("Error loading more comments");
            showmorebtn.show();
            showmorebox.hide();
        }
    });
}

function scrollToId(id) {
    let sId = "#" + id;
    $("html, body").animate(
        {
            scrollTop: $(sId).offset().top
        },
        1000
    );
}

function shareComment(commentId) {
    let shcimage = $(`#share-com-image-${commentId}`);
    shcimage.addClass('clicked');
    shcimage.one('animationend', function () {
        $(this).removeClass('clicked');
    });
    if (navigator.share) {
        navigator.share({
            url: shcomNewUrl(commentId)
        })
            .then(() => {
            })
            .catch((error) => {
            });
    }
}

function copyComment(commentId) {
    navigator.clipboard.writeText(shcomNewUrl(commentId))
        .then(() => {
        })
    let copyCommentElement = $(`#copy-com-image-${commentId}`);
    copyCommentElement.addClass('clicked');

    copyCommentElement.one('animationend', function () {
        $(this).removeClass('clicked');
    });
}

function shcomNewUrl(commentId) {
    let currentUrl = new URL(window.location.href);
    currentUrl.searchParams.set('cri', commentId);
    return currentUrl.toString();
}

function openCCommentModal(forr, category_id, parent_id, reply_to_id = null) {
    $("#ccomReplyModal").modal("show");
    $("#ccom-rep-category-id").val(category_id);
    $("#ccom-rep-parent-id").val(parent_id);
    if (forr == 'replyto') {
        $("#ccom-rep-replyto-id").val(reply_to_id);
    } else {
        $("#ccom-rep-replyto-id").val('');
    }
}