let ccom_like_unlike_is_processing = false;

function likeCategoryComment(comment_id) {
    if (ccom_like_unlike_is_processing) return;
    ccom_like_unlike_is_processing = true;

    let likeImage = $(`#like-com-image-${comment_id}`);
    likeImage.addClass('clicked');
    likeImage.one('animationend', function () {
        $(this).removeClass('clicked');
    });

    $.ajax({
        type: "POST",
        url: '/category-comment-like',
        data: {
            _token: csrf_t,
            like_or_unlike: true,
            category_comment_id: comment_id
        },
        success: function (data) {
            var ellike = "category-comment-like-count-" + comment_id;
            document.getElementById(ellike).innerHTML = data.likecount;

            var elunlike = "category-comment-unlike-count-" + comment_id;
            document.getElementById(elunlike).innerHTML = data.unlikecount;
        },
        complete: function () {
            ccom_like_unlike_is_processing = false;
        }
    });
}

function unlikeCategoryComment(comment_id) {
    if (ccom_like_unlike_is_processing) return; // Prevent further clicks if a request is in progress
    ccom_like_unlike_is_processing = true; // Set the flag to true

    let unlikeImage = $(`#unlike-com-image-${comment_id}`);
    unlikeImage.addClass('clicked');
    unlikeImage.one('animationend', function () {
        $(this).removeClass('clicked');
    });

    $.ajax({
        type: "POST",
        url: '/category-comment-like',
        data: {
            _token: csrf_t,
            like_or_unlike: false,
            category_comment_id: comment_id
        },
        success: function (data) {
            var ellike = "category-comment-like-count-" + comment_id;
            document.getElementById(ellike).innerHTML = data.likecount;

            var elunlike = "category-comment-unlike-count-" + comment_id;
            document.getElementById(elunlike).innerHTML = data.unlikecount;
        },
        complete: function () {
            ccom_like_unlike_is_processing = false;
        }
    });
}

let show_reply_dref_error;
function sendCommentBtnAction() {
    let commentInput = $('#ccom-rep-input');
    let sendButton = $('#ccom-rep-send-btn');
    let page_object_id = $('#ccom-rep-object-id').val();
    let parentId = $('#ccom-rep-parent-id').val();
    let replyToId = $('#ccom-rep-replyto-id').val();
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
        page: page,
        // اگه صفحه نظرات بود ای دی دسته بندی اگه صفحه سوال بود ای دی سوال و ...
        object_id: page_object_id,
        parent_id: parentId,
        reply_id: replyToId,
        body: commentBody,
        _token: csrf_t
    };

    $.ajax({
        url: '/cat-comment-store-dref',
        type: 'POST',
        data: reply_data,
        success: function (response) {
            commentInput.val('');
            $('#ccomReplyModal').modal('hide');
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
        <div class="col-12 px-3 py-2 mr-2 radius-10 mt-1 comment-box text-right reply-div"
        id="reply-box-${comment.id}"
        style="background-color: #a9d3ff !important;">
            <button class="comusr-link">
                <img class="comusr-style rounded-circle" src="${comment.user_image}" alt="${comment.username}">
                <span class="comusr-name">${comment.username}</span>
            </button>
            <p class="textarea-preline mt-2">${comment.body}</p>
            <button type="button" class="comment-reply-btn"
                onclick="openCCommentModal('replyto', '${comment.object_id}', '${comment.parent_id}', '${comment.id}')">پاسخ<img
                    class="mr-1"
                    src="${ftp_path}files/other/images/reply.png">
            </button>
            <span class="like-icon" onclick="likeCategoryComment('${comment.id}')">
                <span id="category-comment-like-count-${comment.id}">${comment.like_count}</span>
                <img class="like-com-image" src="${ftp_path}files/other/images/like-finger.svg" id="like-com-image-${comment.id}">
            </span>
            <span class="dislike-icon" onclick="unlikeCategoryComment('${comment.id}')">
                <span id="category-comment-unlike-count-${comment.id}">${comment.unlike_count}</span>
                <img id="unlike-com-image-${comment.id}" class="unlike-com-image" src="${ftp_path}files/other/images/dislike-finger.svg">
            </span>
        </div>
    `;
    if (comment.reply_id) {
        $('#reply-box-' + comment.reply_id).after(newReplyHtml);
    } else {
        // پاسخ مستقیم به کامنت اصلی
        let repliesBoxId = 'replies-box-' + comment.parent_id;
        let repliesBox = $('#' + repliesBoxId);

        if (repliesBox.length) {
            if (!repliesBox.is(':visible')) {
                repliesBox.show();
                $('#toggle-replies-btn-' + comment.parent_id).text('پنهان کردن پاسخ‌ها');
            }
            repliesBox.append(newReplyHtml);
        } else {
            let newRepliesBoxHtml = `<div class="mt-3" id="${repliesBoxId}">${newReplyHtml}</div>`;
            $('#comment-box-' + comment.parent_id).append(newRepliesBoxHtml);
        }
    }
    $('html, body').animate({
        scrollTop: $('#reply-box-' + comment.id).offset().top - 100
    }, 1000);
    setTimeout(() => {
        $('#reply-box-' + comment.id).css('background-color', '');
    }, 4000);
}

// for send comment after auth
function setActionForAfterAuth(action = null, action_id = null) {
    if (action == null) {
        send_comment_after_login = 0;
    }
    if (action == "comment") {
        let commentInput = $('#cm-input');
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
        let commentInput = $('#ccom-rep-input');
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
        $("#ccomReplyModal").modal("hide");
        $("#login_user").modal("show");
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
    if (!nextPageUrl) {
        $("#load-more-com-btn").hide();
    }
});

function loadMorePosts() {
    let showmorebtn = $("#load-more-com-btn");
    let showmorebox = $("#loadMore");
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

function scrollToId(id, extra_offset = 0) {
    let sId = "#" + id;
    let scrollOffset = $(sId).offset().top - extra_offset;
    $("html, body").animate({
        scrollTop: scrollOffset
    },
        1000
    );
}

setTimeout(() => {
    const criParam = getQueryParam('cri');
    if (criParam) {
        const criValue = 'comment-box-' + criParam;
        if ($("#" + criValue).length) {
            scrollToId(criValue, 100);
        }
    }
}, 1000);



function getQueryParam(param) {
    const urlObj = new URL(window.location.href);
    return urlObj.searchParams.get(param);
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

let copyCommentTimers = {};
function copyComment(commentId) {
    navigator.clipboard.writeText(shcomNewUrl(commentId))
        .then(() => {
        });
    let copyCommentElement = $(`#copy-com-image-${commentId}`);
    copyCommentElement.addClass('clicked');

    copyCommentElement.one('animationend', function () {
        $(this).removeClass('clicked');
    });
    let commentBox = $(`#comment-box-${commentId}`);
    if (copyCommentTimers[commentId]) {
        clearTimeout(copyCommentTimers[commentId]);
        delete copyCommentTimers[commentId];
    }
    commentBox.find('.copy-comment-msg').remove();
    const messageSpan = $('<span class="copy-comment-msg">نظر با موفقیت کپی شد</span>');
    commentBox.append(messageSpan);

    copyCommentTimers[commentId] = setTimeout(() => {
        messageSpan.remove();
        delete copyCommentTimers[commentId];
    }, 4000);
}

function shcomNewUrl(commentId) {
    let currentUrl = new URL(window.location.href);
    currentUrl.searchParams.set('cri', commentId);
    return currentUrl.toString();
}

function openCCommentModal(forr, object_id, parent_id, reply_id = null) {
    $("#ccomReplyModal").modal("show");
    $("#ccom-rep-object-id").val(object_id);
    $("#ccom-rep-parent-id").val(parent_id);
    if (forr == 'replyto') {
        $("#ccom-rep-replyto-id").val(reply_id);
    } else {
        $("#ccom-rep-replyto-id").val('');
    }
}

// for load comment replies
let loadedCommentReplies = [];
let loadingCommentReplies = new Set();

function loadCommentReplies(comment_id) {
    let show_replies_btn = $('#show-replies-btn-' + comment_id);

    // اگر در حال بارگذاری هست یا قبلاً لود شده، برگرد
    if (loadingCommentReplies.has(comment_id) || loadedCommentReplies.includes(comment_id)) {
        return;
    }

    let original_text = show_replies_btn.text();
    show_replies_btn.text('در حال بارگذاری...');
    loadingCommentReplies.add(comment_id); // علامت‌گذاری: در حال لود

    $.ajax({
        url: '/api/get-comment-replies/' + comment_id,
        method: 'GET',
        success: function (response) {
            if (response.html) {
                let repliesBox = $('#replies-box-' + comment_id);
                if (repliesBox.length) {
                    repliesBox.remove();
                }
                $('#comment-box-' + comment_id).append(response.html);

                loadedCommentReplies.push(comment_id);
                show_replies_btn.hide();
                $('#toggle-replies-btn-' + comment_id).show().text('پنهان کردن پاسخ‌ها');
            }
        },
        error: function () {
            // نشان دادن خطا، ولی فعلاً اجازه کلیک نمی‌دیم
            show_replies_btn.text('اینترنت در دسترس نیست').show();

            setTimeout(() => {
                show_replies_btn.text(original_text).show();
                loadingCommentReplies.delete(comment_id); // حالا اجازه بده کلیک کنه
            }, 4000);
        },
        complete: function () {
            // فقط اگر موفق بود و دکمه هنوز هست (یعنی خطا نبود)، پاک کن
            if (!show_replies_btn.is(':visible')) {
                loadingCommentReplies.delete(comment_id);
            }
        }
    });
}

function toggleReplies(comment_id) {
    const box = $('#replies-box-' + comment_id);
    const btn = $('#toggle-replies-btn-' + comment_id);

    if (box.is(':visible')) {
        box.hide();
        btn.text('نمایش پاسخ‌ها');
    } else {
        box.show();
        btn.text('پنهان کردن پاسخ‌ها');
    }
}
// end for load comment replies