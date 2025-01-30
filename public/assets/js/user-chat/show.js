scrollMessageDown();
function sendMessage() {
    let messageInput = $("#user-message-input");
    let trimMessageInput = $.trim(messageInput.val());
    if (trimMessageInput.length == 0) {
        return;
    }
    let send_btn = $(`#send-message-btn`);
    send_btn.prop("disabled", true);
    const formData = new FormData();
    formData.append("message", messageInput.val());
    formData.append("_token", chat_csrf_token);
    $.ajax({
        url: user_send_message_route,
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        beforeSend: function() {},
        success: function(data) {
            if (data.success) {
                send_btn.prop("disabled", false);
                sendMessageTo(messageInput.val(), 1);
                messageInput.val("");
            }
        },
        error: function() {
            send_btn.prop("disabled", false);
        }
    });
}
function scrollMessageDown() {
    $("#chat_div").scrollTop($("#chat_div")[0].scrollHeight);
}

// Echo.private("user-message." + chat_id).listen(".getUserChatMessage", data => {
//     sendMessageTo(data.message, 0);
// });

function sendMessageTo(message, thisUser) {
    if (thisUser == 1) {
        var messageText = "this-user-message";
        var messageDir = "justify-content-start my-2";
        var messageMargin = "mr-3";
    } else {
        var messageText = "that-user-message";
        var messageDir = "justify-content-end my-2";
        var messageMargin = "ml-2";
    }
    let new_chat_html =
        `<div class="row ` +
        messageDir +
        `"><div class="` +
        messageMargin +
        `">
        <p class="` +
        messageText +
        `">
                                    ` +
        message +
        `
                                </p>
                            </div>
                        </div>`;
    $("#chat_div").append(new_chat_html);
    if ($("#message-not-exist").length > 0) {
        $("#message-not-exist").hide();
    }
    scrollMessageDown();
}
