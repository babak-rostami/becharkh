if (page == "show_blog") {
    csrf_t = blog_show_csrf;
}
if (page == "show_question") {
    csrf_t = q_show_csrf;
}
if (page == "show_product") {
    csrf_t = product_show_csrf;
}
function followItem(item_id) {
    let follow_btn = $(`.followi-btn-${item_id}`);
    let last_btn = 0;
    if (follow_btn.hasClass("btn-follow-item")) {
        follow_btn
            .removeClass("btn-follow-item")
            .addClass("btn-following-item");
        follow_btn.prop("disabled", true);
    } else {
        //was followed
        last_btn = 1;
        follow_btn
            .removeClass("btn-nfollow-item")
            .addClass("btn-following-item");
        follow_btn.prop("disabled", true);
    }
    $.ajax({
        type: "POST",
        url: follow_item_route,
        data: {
            _token: csrf_t,
            item_id: item_id
        },
        success: function(data) {
            if (data.follow == 1) {
                if (last_btn == 0) {
                    follow_btn
                        .removeClass("btn-following-item")
                        .addClass("btn-nfollow-item");
                    follow_btn.text("دنبال شده");
                }
            } else {
                if (last_btn == 1) {
                    follow_btn
                        .removeClass("btn-following-item")
                        .addClass("btn-follow-item");
                    follow_btn.text("دنبال کردن");
                }
            }
            follow_btn.prop("disabled", false);
        },
        error: function() {
            if (last_btn == 1) {
                follow_btn
                    .removeClass("btn-following-item")
                    .addClass("btn-nfollow-item");
                follow_btn.text("دنبال شده");
            } else {
                follow_btn
                    .removeClass("btn-following-item")
                    .addClass("btn-follow-item");
                follow_btn.text("دنبال کردن");
            }
            follow_btn.prop("disabled", false);
        }
    });
}
