let uploadingUserImg = false;

if ($("#user-image").length > 0) {
    user_img_input.addEventListener("change", () => {

        let select_img_btn = $('#uimg-select-btn');
        let select_img_btn_last_text = select_img_btn.text();

        if (user_img_input.files.length === 0) {
            return;
        }

        uploadingUserImg = true;

        const file = user_img_input.files[0];
        const fileName = file.name.toLowerCase();
        const mimeType = (file.type || "").toLowerCase();


        if (fileName.endsWith(".heic") || fileName.endsWith(".heif") ||
            mimeType === "image/heic" || mimeType === "image/heif") {
            select_img_btn.text("فرمت HEIC/HEIF پشتیبانی نمی‌شود ❌");
            select_img_btn.addClass('uimg-select-btn-loading');
            setTimeout(() => {
                select_img_btn.text(select_img_btn_last_text);
                select_img_btn.removeClass('uimg-select-btn-loading');
                $('#user-image-input').val('');
                uploadingUserImg = false;
            }, 4000);
            return; // کلا دیگه سمت سرور ارسال نشه
        }

        const formUploadImgData = new FormData();
        formUploadImgData.append("image", file);
        formUploadImgData.append("_token", dash_edit_csrf);

        $.ajax({
            url: '/upload-user-image',
            type: "POST",
            data: formUploadImgData,
            processData: false,
            contentType: false,
            beforeSend: function () {
                select_img_btn.text('در حال ثبت تغییرات...');
                select_img_btn.addClass('uimg-select-btn-loading');
            },
            success: function (data) {
                select_img_btn.text(select_img_btn_last_text);
                select_img_btn.removeClass('uimg-select-btn-loading');
                user_img_preview.src = data.filePath + '?v=' + new Date().getTime();
                uploadingUserImg = false;
            },
            error: function (xhr, status, error) {
                let msg = "خطای ناشناخته‌ای.";
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                select_img_btn.text(msg);
                setTimeout(() => {
                    select_img_btn.text(select_img_btn_last_text);
                    select_img_btn.removeClass('uimg-select-btn-loading');
                    uploadingUserImg = false;
                }, 4000);
            }
        });
    });
}

function openUserImgInput(el) {
    if (!el.classList.contains('uimg-select-btn-loading')) {
        document.getElementById('user-image-input').click();
    }
}

var remainForActive;
let intervalId = setInterval(() => {
    $(".send-active-email-time").text(remainForActive);
    $(".active-email-btn-disabled").css("display", "none");
    $(".active-email-btn").css("display", "inline-block");
    clearInterval(intervalId);
}, 1000);

$(document).ready(function () {
    $(".active-email-btn").on("click", function (e) {
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
                url: '/actice-email',
                success: function (data) {
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
                error: function () {
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
            url: '/user-change-email',
            data: {
                _token: dash_edit_csrf,
                email: email
            },
            success: function (data) {
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
            error: function () {
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

$("#phone").on("input", function () {
    this.value = this.value.replace(/[^0-9]/g, "");
});

$("#phone").on("paste", function (event) {
    event.preventDefault();
});

$(document).ready(function () {
    $('#userUpdateForm').on('keydown', function (event) {
        if (event.key === 'Enter') {
            event.preventDefault();
        }
    });
});

function handleUserUpdateSubmit() {
    $('#update-user-submit-btn').hide();
    $('#update-user-loading-btn').show();
    $('#userUpdateForm').submit();
}

function handleChangeUsernameSubmit() {
    let new_username = $('#new_username').val().trim();
    let submitBtn = $('#chusername-submit');
    let loadingBtn = $('#chusername-loading');
    let msgBox = $("#new_username_msg");

    if (new_username === '') {
        msgBox.text('نام کاربری جدید را بنویسید...').show();
        setTimeout(() => msgBox.fadeOut(), 5000);
        return false;
    }

    // دکمه‌ها و وضعیت
    submitBtn.prop('disabled', true).hide();
    loadingBtn.show().removeClass('btn-success').addClass('btn-light').text('در حال ثبت...');

    $.ajax({
        url: '/request-change-username',
        type: "POST",
        data: {
            new_username: new_username,
            _token: dash_edit_csrf
        },
        success: function (data) {
            if (data.success == 1) {
                loadingBtn.removeClass('btn-light').addClass('btn-success')
                    .text('درخواست با موفقیت ثبت شد');
                $('#new_username').val('');

                setTimeout(() => {
                    loadingBtn.hide();
                    submitBtn.prop('disabled', false).show();
                    loadingBtn.removeClass('btn-success').addClass('btn-light').text('در حال ثبت...');
                }, 7000);
            }
        },
        error: function (jqXHR) {
            let errMsg = jqXHR.responseJSON?.message || "خطایی رخ داد، دوباره تلاش کنید";
            msgBox.text(errMsg).show();

            setTimeout(() => msgBox.fadeOut(), 8000);
            loadingBtn.hide();
            submitBtn.prop('disabled', false).show();
        }
    });
}


if ($("#new_username").length > 0) {
    $("#new_username")
        .on("input", function () {
            $("#new_username_msg").text('');
            $("#new_username_msg").hide();

            let input = this.value;
            input = input.replace(/[^a-zA-Z0-9_.]/g, "").toLowerCase();

            // نذاره با عدد شروع بشه
            if (/^\d/.test(input)) {
                input = input.replace(/^\d+/, "");
            }

            // محدود کردن طول به 25 کاراکتر
            if (input.length > 25) {
                input = input.substring(0, 25);
            }

            this.value = input;
        })
        .on("paste", function (event) {
            event.preventDefault(); // جلوگیری از چسباندن متن
        })
        .on("blur", function () {
            let input = this.value;
            input = input.replace(/[^a-zA-Z0-9_.]/g, "").toLowerCase();
            if (/^\d/.test(input)) {
                input = input.replace(/^\d+/, "");
            }
            if (input.length > 25) {
                input = input.substring(0, 25);
            }
            this.value = input;
        })
        .attr("autocomplete", "off"); // غیرفعال کردن autocomplete
}

let currentField = null;
let errorTimeout = null;

function editAccount(field) {
    currentField = field;
    let value = "";
    let modalTitle = "";

    if (field === "name") {
        value = $("#uuform-info-name").next().text().trim();
        if (value === "نام ثبت نشده") value = "";
        modalTitle = "ویرایش نام";
    } else if (field === "phone") {
        value = $("#uuform-info-phone").next().text().trim();
        if (value === "شماره تلفن ثبت نشده") value = "";
        modalTitle = "ویرایش شماره تلفن";
    } else if (field === "body") {
        value = $("#uuform-info-body").next().text().trim();
        if (value === "درباره‌‌ی خود بنویسید...") value = "";
        modalTitle = "ویرایش بیوگرافی";
    }

    let inputHtml = "";

    if (field === "name") {
        inputHtml = `<input type="text" id="edit_input" class="form-control"
                     maxlength="25" value="${value}"
                     placeholder="نام خود را وارد کنید">`;
    } else if (field === "phone") {
        inputHtml = `<input type="text" id="edit_input" class="form-control"
                     value="${value}" placeholder="شماره تلفن"
                     oninput="this.value=this.value.replace(/[^0-9]/g,'').substring(0,15)">`;
    } else if (field === "body") {
        inputHtml = `<textarea id="edit_input" class="form-control" rows="4"
                     placeholder="بیوگرافی">${value}</textarea>`;
    }

    $("#account_edit_field").html(inputHtml);
    $("#account_edit_error").hide().text("");
    $("#account_edit_title").text(modalTitle);
    $("#account_edit").modal("show");
}

function showError(message) {
    if (errorTimeout) {
        clearTimeout(errorTimeout);
        errorTimeout = null;
    }

    $("#account_edit_error").text(message).fadeIn();

    errorTimeout = setTimeout(() => {
        $("#account_edit_error").fadeOut();
    }, 5000);
}

function userUpdate() {
    let value = $("#edit_input").val().trim();
    let errorBox = $("#account_edit_error");
    let update_btn = $("#user-update-btn");

    errorBox.hide().text("");

    if (!value) {
        showError("این فیلد نمی‌تواند خالی باشد.");
        return;
    }

    update_btn.prop("disabled", true).text("⏳ در حال ثبت تغییرات...");

    $.ajax({
        url: "/user-update",
        type: "POST",
        data: {
            _token: dash_edit_csrf,
            _method: "PUT",
            [currentField]: value
        },
        success: function (res) {
            if (res.success) {
                if (currentField === "name") {
                    $("#uuform-info-name").next().text(value);
                } else if (currentField === "phone") {
                    $("#uuform-info-phone").next().text(value);
                } else if (currentField === "body") {
                    $("#uuform-info-body").next().text(value);
                }

                $("#account_edit").modal("hide");
            }
        },
        error: function (xhr) {
            if (xhr.status === 422) {
                let errors = xhr.responseJSON.errors;
                if (errors) {
                    let firstError = Object.values(errors)[0][0];
                    showError(firstError);
                }
            } else {
                showError("خطایی رخ داد. دوباره تلاش کنید.");
            }
        },
        complete: function () {
            update_btn.prop("disabled", false).text("ثبت تغییرات");
        }
    });

    // تابع نمایش خطا
    function showError(msg) {
        clearTimeout(errorBox.data("timeout"));
        errorBox.text(msg).show();

        let timeout = setTimeout(() => {
            errorBox.fadeOut();
        }, 5000);

        errorBox.data("timeout", timeout);
    }
}

