if ($("#user-image").length > 0) {
    user_img_input.addEventListener("change", () => {
        const formUploadImgData = new FormData();
        if (user_img_input.files.length === 0) {
            return;
        }
        formUploadImgData.append("image", user_img_input.files[0]);
        formUploadImgData.append("_token", dash_edit_csrf);

        $.ajax({
            url: upload_user_img_route,
            type: "POST",
            data: formUploadImgData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                $("#loading-image-icon").show();
            },
            success: function(data) {
                $("#loading-image-icon").hide();
                user_img_preview.src = data.filePath;
            },
            error: function() {
                $("#loading-image-icon").hide();
            }
        });
    });
}

var remainForActive;
let intervalId = setInterval(() => {
    $(".send-active-email-time").text(remainForActive);
    $(".active-email-btn-disabled").css("display", "none");
    $(".active-email-btn").css("display", "inline-block");
    clearInterval(intervalId);
}, 1000);

$(document).ready(function() {
    $(".active-email-btn").on("click", function(e) {
        setTimeout(() => {
            $(".active-email-btn").removeAttr("href");
        }, 50);
    });
    open_img = $("<img />", {
        src: b_add,
        alt: "minimize icon",
        class: "open-img"
    });
    open_img.appendTo(".post-info-closed");
    // openEditPostBox(first_post);
});

// function openEditPostBox(post_id) {
//     edit_btn = $(`#open-edit-post-btn-${post_id}`);
//     edit_div = $(`#post-edit-${post_id}`);
//     minimize_img = $("<img />", {
//         src: b_min,
//         alt: "minimize icon",
//         class: "min-img"
//     });
//     open_img = $("<img />", {
//         src: b_add,
//         alt: "minimize icon",
//         class: "open-img"
//     });
//     if (edit_btn.hasClass("post-info-closed")) {
//         edit_btn.removeClass("post-info-closed").addClass("post-info-opend");
//         edit_div.slideDown(1000);
//         edit_btn.html(minimize_img);
//     } else {
//         edit_btn.removeClass("post-info-opend").addClass("post-info-closed");
//         edit_div.slideUp(1000);
//         edit_btn.html(open_img);
//     }
// }

function loadingBtn(button) {
    button.innerHTML = `<img src="${loading_gif}" alt="Loading...">`;
    setTimeout(() => {
        button.type = "button";
    }, 10);
    button.classList.remove("btn-success");
}

if (typeof last_active_code !== "undefined") {
    if (last_active_code < 90) {
        increaseLastActiveTime();
    }

    function increaseLastActiveTime() {
        let intervalId = setInterval(() => {
            last_active_code += 1;
            if (last_active_code >= 90) {
                clearInterval(intervalId);
            }
        }, 1000);
    }

    function activeEmail() {
        if (last_active_code < 90) {
            let timeoutId = setInterval(() => {
                diff = 90 - last_active_code;
                if (diff > 0) {
                    $("#active-email-btn").prop("disabled", true);
                    $("#active-email-msg").text(
                        `برای ارسال مجدد ${diff} ثانیه صبر کنید`
                    );
                } else {
                    clearInterval(timeoutId);
                    $("#active-email-btn").prop("disabled", false);
                    $("#active-email-msg").text("ارسال مجدد؟");
                }
            }, 1000);
        } else {
            $("#active-email-btn")
                .removeClass("btn-primary")
                .addClass("btn-light");
            $("#active-email-btn").prop("disabled", true);
            $("#active-email-btn").html(`<img src="${loading_gif}">`);
            $.ajax({
                type: "GET",
                url: active_email_route,
                success: function(data) {
                    $("#active-email-msg").text(data.message);
                    if (data.success == 1) {
                        last_active_code = 0;
                        increaseLastActiveTime();
                        $("#active-email-btn")
                            .removeClass("btn-light")
                            .addClass("btn-primary");
                        $("#active-email-btn").text("ارسال مجدد لینک فعالسازی");
                        $("#active-email-btn").prop("disabled", false);
                    }
                },
                error: function() {
                    $("#active-email-btn")
                        .removeClass("btn-light")
                        .addClass("btn-primary");
                    $("#active-email-btn").text("ارسال مجدد لینک فعالسازی");
                    $("#active-email-btn").prop("disabled", false);
                }
            });
        }
    }

    function changeEmail() {
        let email = $("#change-email-input").val();
        if (!/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/.test(email)) {
            $("#change-email-msg").text("آدرس ایمیل اشتباه است!");
            setTimeout(() => {
                $("#change-email-msg").text("");
            }, 3000);
            return;
        }
        $("#change-email-btn")
            .removeClass("btn-primary")
            .addClass("btn-light");
        $("#change-email-btn").prop("disabled", true);
        $("#change-email-btn").html(`<img src="${loading_gif}">`);
        $.ajax({
            type: "POST",
            url: change_email_route,
            data: {
                _token: dash_edit_csrf,
                email: email
            },
            success: function(data) {
                if (data.error == 1) {
                    $("#change-email-msg").text(data.message);
                    setTimeout(() => {
                        $("#change-email-msg").text("");
                    }, 3000);

                    $("#change-email-btn")
                        .removeClass("btn-light")
                        .addClass("btn-primary");
                    $("#change-email-btn").prop("disabled", false);
                    $("#change-email-btn").text("تغییر ایمیل");
                } else {
                    $("#change-email-ue").text(email);
                    $("#active-email-msg").text("");
                    $("#change-email-input").val("");
                    $("#change_email").modal("hide");
                    $("#change-email-btn")
                        .removeClass("btn-light")
                        .addClass("btn-primary");
                    $("#change-email-btn").prop("disabled", false);
                    $("#change-email-btn").text("تغییر ایمیل");
                    last_active_code = 91;
                    activeEmail();
                }
            },
            error: function() {
                $("#change-email-btn")
                    .removeClass("btn-light")
                    .addClass("btn-primary");
                $("#change-email-btn").prop("disabled", false);
                $("#change-email-btn").text("تغییر ایمیل");
            }
        });
    }
}

function payAd(ad_id) {
    $(`#pay-ad-btn-${ad_id}`).hide();
    $(`#pay-ad-btn-loading-${ad_id}`).show();
}
function rockAd(ad_id) {
    $(`#rock-ad-btn-${ad_id}`).hide();
    $(`#rock-ad-btn-loading-${ad_id}`).show();
}

$("#phone").on("input", function() {
    this.value = this.value.replace(/[^0-9]/g, "");
});

$("#phone").on("paste", function(event) {
    event.preventDefault();
});
