let choose_surop_is_processing = false;

function chooseSurOp(obj_id, option_number) {
    if (choose_surop_is_processing) return;
    choose_surop_is_processing = true;
    let cs = null;
    let page_name = null;

    if (page == "comment") {
        cs = csrf_t;
        page_name = "comment";
    } else if (page == "show_question") {
        cs = q_show_csrf;
        page_name = "show_question";
    }

    $.ajax({
        url: route_surop_choose, // Replace with your actual route
        type: "POST",
        data: {
            page: page_name,
            obj_id: obj_id,
            option_number: option_number,
            _token: cs
        },
        success: function (data) {
            if (data.surop1_count) {
                let surop1 = data.surop1_count.split("-");
                $("#suropshow-" + obj_id + "-1").text(
                    `${surop1[0]} (${parseFloat(surop1[1]).toFixed(2)}%)`
                );
            }
            if (data.surop2_count) {
                let surop2 = data.surop2_count.split("-");
                $("#suropshow-" + obj_id + "-2").text(
                    `${surop2[0]} (${parseFloat(surop2[1]).toFixed(2)}%)`
                );
            }
            if (data.surop3_count) {
                let surop3 = data.surop3_count.split("-");
                $("#suropshow-" + obj_id + "-3").text(
                    `${surop3[0]} (${parseFloat(surop3[1]).toFixed(2)}%)`
                );
            }
            if (data.surop4_count) {
                let surop4 = data.surop4_count.split("-");
                $("#suropshow-" + obj_id + "-4").text(
                    `${surop4[0]} (${parseFloat(surop4[1]).toFixed(2)}%)`
                );
            }
        },
        error: function (jqXHR) {
            console.error("Error in AJAX request:", jqXHR);
        },
        complete: function () {
            choose_surop_is_processing = false;
        }
    });
}
