var typingTimer; // timer identifier
var doneTypingInterval = 1000; // time in ms, 1 second for example

// on keyup, start the countdown
$("#qe_search_input").on("keyup", function() {
    clearTimeout(typingTimer);

    typingTimer = setTimeout(() => {
        qe_search_items = -1;
        qe_search_categories = -1;
        qe_search_questions = -1;
        qe_search_blogs = -1;
        searchForInput();
    }, doneTypingInterval);
});

// on keydown, clear the countdown
$("#qe_search_input").on("keydown", function(event) {
    if (event.key !== "Control" && !event.key.startsWith("Arrow")) {
        clearTimeout(typingTimer);
    }
});

let qe_search_for = 1;
let qe_search_input;
let qe_search_items = -1;
let qe_search_categories = -1;
function searchForInput() {
    qe_search_input = $("#qe_search_input").val();

    if (qe_search_input?.length <= 2) {
        $("#show-qesearch-empty").show();
        $("#show-qesearch-result").hide();
        return;
    }
    $("#show-qesearch-result").hide();
    $("#show-qesearch-empty").hide();
    $("#show-qesearch-loading").show();

    $.ajax({
        method: "get",
        url: "/main-search/" + qe_search_for + "/" + qe_search_input,
        success: function(data) {
            $("#show-qesearch-loading").hide();
            $("#show-qesearch-result").show();
            if (qe_search_for == 1) {
                qe_search_items = data.items;
                qe_search_categories = data.categories;
                showMainSearchItemsResults();
            }
        }
    });
}

let users = [];

function getCategoyEmailUser(category_id) {
    $.ajax({
        method: "get",
        url: get_cat_eusers_route,
        data: {
            category_id: category_id,
            question_id: question_id
        },
        success: function(data) {
            data.users.forEach(user => {
                if (!users.find(u => u.id === user.id)) {
                    users.push(user);
                    addToSelectUserList(user);
                }
            });
        }
    });
}
function getItemEmailUser(item_id) {
    $.ajax({
        method: "get",
        url: get_item_eusers_route,
        data: {
            item_id: item_id,
            question_id: question_id
        },
        success: function(data) {
            data.users.forEach(user => {
                if (!users.find(u => u.id === user.id)) {
                    users.push(user);
                    addToSelectUserList(user);
                }
            });
        }
    });
}

function addToSelectUserList(user) {
    let user_div = `<div class="user-item" id="us-${user.id}" onclick="selectUser('${user.id}')">
                <img src="${user.image}">
                <span class="float-left">${user.username}</span>
                <span>${user.email}</span>
            </div>`;
    $("#select-user-list").prepend(user_div);
}

let selected_users = [];
function selectUser(user_id) {
    let user_span = $(`#us-${user_id}`);
    if (!selected_users.find(u => u.id === user_id)) {
        selected_users.push({ id: user_id });
        user_span.addClass("user-item-selected");
    } else {
        selected_users = selected_users.filter(u => u.id !== user_id);
        user_span.removeClass("user-item-selected");
    }
}

function showMainSearchItemsResults() {
    qe_search_for = 1;
    let result_box;
    result_box = $("#show-qesearch-result");
    result_box.empty();
    if (qe_search_categories == -1 || qe_search_items == -1) {
        searchForInput();
    } else {
        if (qe_search_categories.length > 0) {
            let cat_items = "";
            qe_search_categories.forEach(cat => {
                cat_items += `<div class="qesearch-result-cat">
                <a class="qesearch-result-cat-a" onclick="getCategoyEmailUser('${cat.id}')">
                <img class="qesearch-result-cat-img" src="${cat.img}">
                <span>${cat.title}</span> 
                </a>
            </div>
            `;
            });
            result_box.append(
                `<span id="qesearch-result-h">دسته بندی ها</span>
            <div id="qesearch-result-cats">${cat_items}</div>`
            );
        }
        if (qe_search_items.length > 0) {
            qe_search_items.forEach(item => {
                result_box.append(`
            <div class="qesearch-result-item" onclick="getItemEmailUser('${item.id}')">
                <img class="qesearch-result-item-img" src="${item.img}">
                <span class="qesearch-result-item-title">${item.title}</span> 
                <span class="qesearch-result-item-cat">${item.cat}</span>
            </div>
            `);
            });
        }
        if (qe_search_items.length == 0 && qe_search_categories.length == 0) {
            result_box.append(`<div id="qesearch-result-404">
             نتیجه ای پیدا نشد! </div>`);
        }
    }
}

function sendEmail() {
    let semail_btn = $("#send-email-btn");
    let email_title = $("#email-title").val();
    let email_message_span = $("#send-email-message");
    if (email_title == null || email_title == "") {
        email_message_span.show();
        email_message_span.text("عنوان ایمیل را وارد کنید");
        setTimeout(() => {
            email_message_span.hide();
        }, 5000);
        return;
    }
    semail_btn.removeClass("btn-primary").addClass("btn-dark");
    semail_btn.removeAttr("onclick");
    semail_btn.off("click");
    $.ajax({
        type: "POST",
        url: send_email_route,
        data: {
            _token: csrf_token,
            title: email_title,
            users: selected_users,
            question_id: question_id
        },
        success: function(data) {
            semail_btn.on("click", function() {
                sendEmail();
            });
            semail_btn.removeClass("btn-dark").addClass("btn-primary");
            if (data.status == 1) {
                email_message_span.show();
                email_message_span.text("با موفقیت به صف ارسال ایمیل اضافه شد");
                setTimeout(() => {
                    email_message_span.hide();
                }, 5000);
            }
        },
        error: function(error) {
            semail_btn.on("click", function() {
                sendEmail();
            });
            semail_btn.removeClass("btn-dark").addClass("btn-primary");
            email_message_span.show();
            email_message_span.text(error.responseJSON.message);
            setTimeout(() => {
                email_message_span.hide();
            }, 5000);
        }
    });
}
