$(document).ready(function () {
    if (getCookie("page_seen") == null) {
        setCookie("page_seen", "1", 3600, window.location.pathname);
    }
});

//for send comment after auth
function setActionForAfterAuth(action = null, action_id = null) {
    if (action == null) {
        send_comment_after_login = 0;
    }
    if (action == "answer") {
        let commentInput = $('#qa-textarea');
        let commentBody = commentInput.val();
        if (commentBody.trim() === "") {
            if (show_reply_dref_error) {
                clearTimeout(show_reply_dref_error);
            }
            $('#error-message').remove();
            $('<span id="error-message">دیدگاه خود را بنویسید.</span>').insertAfter(commentInput);
            show_reply_dref_error = setTimeout(() => {
                $('#error-message').remove();
            }, 4000);
            return;
        } else {
            $('#error-message').remove();
        }
        send_comment_after_login = action_id;
        $("#login_user").modal("show");
    }
    if (action == "reply") {
        let commentInput = $('#qa-rep-input');
        let commentBody = commentInput.val();
        if (commentBody.trim() === "") {
            if (show_reply_dref_error) {
                clearTimeout(show_reply_dref_error);
            }
            $('#error-message').remove();
            $('<span id="error-message">دیدگاه خود را بنویسید.</span>').insertAfter(commentInput);
            show_reply_dref_error = setTimeout(() => {
                $('#error-message').remove();
            }, 4000);
            return;
        } else {
            $('#error-message').remove();
        }
        send_comment_after_login = action_id;
        $("#qa-reply-modal").modal("hide");
        $("#login_user").modal("show");
    }
}
function doThisAfterAuth() {
    if (send_comment_after_login != 0) {
        let sendButton = $('#qa-rep-send-btn');
        sendButton.prop('disabled', true);
        sendButton.text('در حال ثبت نظر...');
        sendButton.removeClass('btn-outline-primary').addClass('btn-light');
        formObj = $(`#${send_comment_after_login}`);
        formObj.submit();
    } else {
        location.reload();
    }
}
//end for send comment after auth

$("#cm-input").on("input", function () {
    this.style.height = "auto";
    this.style.height = this.scrollHeight + "px";
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

let ans_like_unlike_is_processing = false; // Flag to track if a like or unlike request is in progress

function like(question_answer_id) {
    if (ans_like_unlike_is_processing) return;
    ans_like_unlike_is_processing = true;

    let likeImage = $(`#like-ans-image-${question_answer_id}`);
    likeImage.addClass('clicked');
    likeImage.one('animationend', function () {
        $(this).removeClass('clicked');
    });

    $.ajax({
        type: "POST",
        url: q_answer_like_route,
        data: {
            _token: q_show_csrf,
            like_or_unlike: true,
            question_answer_id: question_answer_id
        },
        success: function (data) {
            let ellike = "like-ans-count-" + question_answer_id;
            document.getElementById(ellike).innerHTML = data.likecount;

            let elunlike = "unlike-ans-count-" + question_answer_id;
            document.getElementById(elunlike).innerHTML = data.unlikecount;
        },
        complete: function () {
            ans_like_unlike_is_processing = false;
        }
    });
}

function unlike(question_answer_id) {
    if (ans_like_unlike_is_processing) return;
    ans_like_unlike_is_processing = true;

    let unlikeImage = $(`#unlike-ans-image-${question_answer_id}`);
    unlikeImage.addClass('clicked');
    unlikeImage.one('animationend', function () {
        $(this).removeClass('clicked');
    });

    $.ajax({
        type: "POST",
        url: q_answer_like_route,
        data: {
            _token: q_show_csrf,
            like_or_unlike: false,
            question_answer_id: question_answer_id
        },
        success: function (data) {
            let ellike = "like-ans-count-" + question_answer_id;
            document.getElementById(ellike).innerHTML = data.likecount;

            let elunlike = "unlike-ans-count-" + question_answer_id;
            document.getElementById(elunlike).innerHTML = data.unlikecount;
        },
        complete: function () {
            ans_like_unlike_is_processing = false;
        }
    });
}

/* for bslider */
const catSlider = document.getElementById("cat-slider");
createSlider(catSlider, "cat-slider-item");

function openAnsReplyModal(forr, question_id, parent_id, reply_id = null) {
    $("#qa-reply-modal").modal("show");
    $("#qa-rep-question-id").val(question_id);
    $("#qa-rep-parent-id").val(parent_id);
    if (forr == 'replyToRep') {
        $("#qa-rep-replyto-id").val(reply_id);
    } else {
        $("#qa-rep-replyto-id").val('');
    }
}

let show_reply_dref_error;
function sendCommentBtnAction() {
    let commentInput = $('#qa-rep-input');
    let sendButton = $('#qa-rep-send-btn');
    let questionId = $('#qa-rep-question-id').val();
    let parentId = $('#qa-rep-parent-id').val();
    let replyToId = $('#qa-rep-replyto-id').val();
    let commentBody = commentInput.val();

    if (commentBody.trim() === "") {
        if (show_reply_dref_error) {
            clearTimeout(show_reply_dref_error);
        }
        $('#error-message').remove();
        $('<span id="error-message">دیدگاه خود را بنویسید.</span>').insertAfter(commentInput);
        show_reply_dref_error = setTimeout(() => {
            $('#error-message').remove();
        }, 4000);
        return;
    } else {
        $('#error-message').remove();
    }

    sendButton.prop('disabled', true);
    sendButton.text('در حال ثبت نظر...');
    sendButton.removeClass('btn-outline-primary').addClass('btn-light');

    let reply_data = {
        question_id: questionId,
        parent_id: parentId,
        reply_id: replyToId,
        body: commentBody,
        _token: q_show_csrf
    };

    $.ajax({
        url: reply_df_route,
        type: 'POST',
        data: reply_data,
        success: function (response) {
            commentInput.val('');
            $('#qa-reply-modal').modal('hide');
            addHtmlOnSuccess(response.comment);
        },
        error: function (xhr, status, error) {
            let errorMessage = xhr.responseJSON && xhr.responseJSON.error ? xhr.responseJSON.error : 'خطایی رخ داد.';
            if (show_reply_dref_error) {
                clearTimeout(show_reply_dref_error);
            }
            $('#error-message').remove();
            $(`<span id="error-message">${errorMessage}</span>`).insertAfter(commentInput);
            show_reply_dref_error = setTimeout(() => {
                $('#error-message').remove();
            }, 4000);
        },
        complete: function () {
            sendButton.text('ثبت نظر');
            sendButton.prop('disabled', false);
            sendButton.removeClass('btn-light').addClass('btn-outline-primary');
        }
    });
}

function addHtmlOnSuccess(comment) {
    let newReplyHtml = `
        <div class="row py-3 radius-10 mr-3 ml-0 mt-1 reply-box" id="reply-box-${comment.id}"
            style="background-color: #a9d3ff !important;">
            <div class="col-12">
                <button class="comusr-link">
                    <img class="comusr-style rounded-circle" src="${comment.user_image}" alt="${comment.username}">
                    <span class="comusr-name">${comment.username}</span>
                </button>
                <p class="mt-4 font-18 textarea-preline">${comment.body}</p>
                <button type="button" class="comment-reply-btn"
                    onclick="openAnsReplyModal('replyToRep', '${comment.question_id}', '${comment.parent_id}', '${comment.id}')">پاسخ<img
                        class="mr-1"
                        src="${ftp_path}files/other/images/reply.png">
                </button>
                <span class="like-icon" onclick="like('${comment.id}')">
                    <span id="like-ans-count-${comment.id}">${comment.like_count}</span>
                    <img class="like-ans-image" id="like-ans-image-${comment.id}" src="${ftp_path}files/other/images/like-finger.svg">
                </span>
                <span class="dislike-icon" onclick="unlike('${comment.id}')">
                    <span id="unlike-ans-count-${comment.id}">${comment.unlike_count}</span>
                    <img class="unlike-ans-image" id="unlike-ans-image-${comment.id}" src="${ftp_path}files/other/images/dislike-finger.svg">
                </span>
            </div>
        </div>
    `;
    if (comment.reply_id) {
        $('#reply-box-' + comment.reply_id).after(newReplyHtml);
    } else {
        $('#answer-box-' + comment.parent_id).after(newReplyHtml);
    }
    $('html, body').animate({
        scrollTop: $('#reply-box-' + comment.id).offset().top - 100
    }, 1000);
    setTimeout(() => {
        $('#reply-box-' + comment.id).css('background-color', '');
    }, 4000);
}