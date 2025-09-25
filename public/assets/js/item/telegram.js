let itelMsgTimer = null;

function showMessage(html, type = "info") {
    let msgBox = $('#itb-message');
    msgBox.html('<span class="text-' + type + '">' + html + '</span>');

    if (itelMsgTimer) clearTimeout(itelMsgTimer);
    itelMsgTimer = setTimeout(() => {
        msgBox.fadeOut(300, function () {
            $(this).html('').show();
        });
    }, 5000);
}

function saveTelNumber() {
    let phone = $('#itb-phone-input').val().trim();
    let item_id = $('#item_id').val();
    let btn = $('#itb-copy-btn');

    if (phone.length < 10) {
        showMessage('شماره معتبر نیست.', 'danger');
        return;
    }

    if (typeof page !== "undefined") {
        if (page === "comment") {
            csrf_itel_num = csrf_t;
        }
        if (page === "show_question") {
            csrf_itel_num = q_show_csrf;
        }
    }

    $.ajax({
        url: "/item-tel-save-number",
        type: "POST",
        data: {
            _token: csrf_itel_num,
            phone: phone,
            item_id: item_id
        },
        beforeSend: function () {
            btn.prop('disabled', true).text('در حال ذخیره...');
        },
        success: function (response) {
            if (response.success) {
                btn.text('ثبت شد، بزودی اضافه می‌شوید.');
                setTimeout(() => {
                    btn.prop('disabled', false).text('عضویت');
                }, 8000);
                $('#itb-phone-input').val('');
            } else {
                showMessage(response.message || "خطایی رخ داد.", 'danger');
            }
        },
        error: function (xhr) {
            if (xhr.responseJSON && xhr.responseJSON.message) {
                showMessage(xhr.responseJSON.message, 'danger');
            } else {
                showMessage('ارتباط با سرور برقرار نشد.', 'danger');
            }
            btn.prop('disabled', false).text('عضویت');
        }
    });
}