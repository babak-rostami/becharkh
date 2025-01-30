$(document).ready(function () {
    if (getCookie("page_seen") == null) {
        setCookie("page_seen", "1", 3600, window.location.pathname);
    }
});

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

function likeProductComment(product_comment_id) {
    $.ajax({
        type: "POST",
        url: product_comment_like_route,
        data: {
            _token: product_show_csrf,
            like_or_unlike: true,
            product_comment_id: product_comment_id
        },
        success: function (data) {
            var ellike = "product-comment-like-count-" + product_comment_id;
            document.getElementById(ellike).innerHTML = data.likecount;

            var elunlike = "product-comment-unlike-count-" + product_comment_id;
            document.getElementById(elunlike).innerHTML = data.unlikecount;
        }
    });
}

function unlikeProductComment(product_comment_id) {
    $.ajax({
        type: "POST",
        url: product_comment_like_route,
        data: {
            _token: product_show_csrf,
            like_or_unlike: false,
            product_comment_id: product_comment_id
        },
        success: function (data) {
            var ellike = "product-comment-like-count-" + product_comment_id;
            document.getElementById(ellike).innerHTML = data.likecount;

            var elunlike = "product-comment-unlike-count-" + product_comment_id;
            document.getElementById(elunlike).innerHTML = data.unlikecount;
        }
    });
}

function submitCommentClick(forr, id) {
    if (forr == "reply2") {
        let reply2_btn = $(`#reply2-btn-${id}`);
        reply2_btn.prop("disabled", true);
        let textareaValue = $(`#body2-${id}`)
            .val()
            .trim();
        if (textareaValue === "") {
            reply2_btn.prop("disabled", false);
        } else {
            $(`#replytoForm-${id}`).submit();
        }
    } else if (forr == "reply1") {
        let reply_btn = $(`#reply1-btn-${id}`);
        reply_btn.prop("disabled", true);
        let textareaValue = $(`#body1-${id}`)
            .val()
            .trim();
        if (textareaValue === "") {
            reply_btn.prop("disabled", false);
        } else {
            $(`#replyForm-${id}`).submit();
        }
    }
}

const catSlider = document.getElementById("cat-slider");
if (catSlider) {
    createSlider(catSlider, "cat-slider-item");
}