$(document).ready(function() {
    // Check if the 'page_seen' cookie is set for the current path
    if (getCookie("page_seen") == null) {
        // Set the 'page_seen' cookie for the current path with an expiration time of 1 hour
        setCookie("page_seen", "1", 3600, window.location.pathname);
    }
});

//for send comment after auth
function setActionForAfterAuth(action = null, action_id = null) {
    if (action == null) {
        send_comment_after_login = 0;
    }
    if (action == "answer") {
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

$("#cm-input").on("input", function() {
    this.style.height = "auto";
    this.style.height = this.scrollHeight + "px";
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


$("#like-btn").click(function(e) {
    e.preventDefault();
    $("#like-btn").css("display", "none");
    $.ajax({
        type: "POST",
        url: q_like_route,
        data: {
            _token: q_show_csrf,
            question_id: question_id,
            like_or_unlike: $("#like-span").text()
        },
        success: function(result) {
            if (result.is_like == "لایک") {
                $("#like-span").text("لایک شد");
                $("#like-btn").removeClass("btn-outline-danger");
                $("#like-btn").addClass("btn-outline-success");
            } else {
                $("#like-span").text("لایک");
                $("#like-btn").removeClass("btn-outline-success");
                $("#like-btn").addClass("btn-outline-danger");
            }
            $("#like-count-span").text(result.likes_count);
        },
        complete: function() {
            $("#like-btn").css("display", "inline-block");
        }
    });
});

function like(question_answer_id) {
    $.ajax({
        type: "POST",
        url: q_answer_like_route,
        data: {
            _token: q_show_csrf,
            like_or_unlike: true,
            question_answer_id: question_answer_id
        },
        success: function(data) {
            var ellike = "like-answer-count-" + question_answer_id;
            document.getElementById(ellike).innerHTML = data.likecount;

            var elunlike = "unlike-answer-count-" + question_answer_id;
            document.getElementById(elunlike).innerHTML = data.unlikecount;
        }
    });
}

function unlike(question_answer_id) {
    $.ajax({
        type: "POST",
        url: q_answer_like_route,
        data: {
            _token: q_show_csrf,
            like_or_unlike: false,
            question_answer_id: question_answer_id
        },
        success: function(data) {
            var ellike = "like-answer-count-" + question_answer_id;
            document.getElementById(ellike).innerHTML = data.likecount;

            var elunlike = "unlike-answer-count-" + question_answer_id;
            document.getElementById(elunlike).innerHTML = data.unlikecount;
        }
    });
}

/* for bslider */
const catSlider = document.getElementById("cat-slider");
createSlider(catSlider, "cat-slider-item");
