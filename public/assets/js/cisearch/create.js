let cisearch_typingTimer; // timer identifier
let cisearch_doneTypingInterval = 1000; // time in ms, 1 second for example

// on keyup, start the countdown
$("#cisearch_input").on("keyup", function () {
    clearTimeout(cisearch_typingTimer);

    cisearch_typingTimer = setTimeout(() => {
        cisearch_items = -1;
        cisearch_categories = -1;
        cisearch_questions = -1;
        cisearch_blogs = -1;
        searchForInput();
    }, cisearch_doneTypingInterval);
});

// on keydown, clear the countdown
$("#cisearch_input").on("keydown", function (event) {
    if (event.key !== "Control" && !event.key.startsWith("Arrow")) {
        clearTimeout(cisearch_typingTimer);
    }
});

let cisearch_for = 1;
let cisearch_input;
let cisearch_items = -1;
let cisearch_categories = -1;
let cisearch_questions = -1;

function searchForInput() {
    cisearch_input = $("#cisearch_input").val();

    if (cisearch_input?.length <= 2) {
        $("#show-cisearch-empty").show();
        $("#show-cisearch-result").hide();
        return;
    }
    $("#show-cisearch-result").hide();
    $("#show-cisearch-empty").hide();
    $("#show-cisearch-loading").show();

    $.ajax({
        method: "get",
        url: "/main-search/" + cisearch_for + "/" + cisearch_input,
        success: function (data) {
            $("#show-cisearch-loading").hide();
            $("#show-cisearch-result").show();
            if (cisearch_for == 1) {
                cisearch_items = data.items;
                cisearch_categories = data.categories;
                showMainSearchItemsResults();
            }
        }
    });
    if ($("#questions").length) {
        $.ajax({
            method: "get",
            url: "/main-search/2/" + cisearch_input,
            success: function (data) {
                $("#show-cisearch-loading").hide();
                $("#show-cisearch-result").show();
                if (cisearch_for == 1) {
                    cisearch_questions = data.questions;
                    showMainSearchForumResults();
                }
            }
        });
    }
}

function showMainSearchForumResults() {
    result_box = $("#show-cisearch-result");
    if (cisearch_questions.length > 0) {
        cisearch_questions.forEach(question => {
            result_box.append(
                `<a class="cisearch-question" onclick="selectQuestion('${question.id}', '${question.title}')">
                ${question.title}</a>`
            );
        });
    } else {
        result_box.append(`<div id="cisearch-404">
             نتیجه ای پیدا نشد! </div>`);
    }
}
function showMainSearchItemsResults() {
    cisearch_for = 1;
    let result_box;
    result_box = $("#show-cisearch-result");
    result_box.empty();
    if (cisearch_categories == -1 || cisearch_items == -1) {
        searchForInput();
    } else {
        if ($("#categories").length && cisearch_categories.length > 0) {
            let cat_items = "";
            cisearch_categories.forEach(cat => {
                cat_items += `<div class="cisearch-result-cat">
                <a class="cisearch-result-cat-a" target="_blank"  onclick="selectCategory('${cat.id}','${cat.title}')">
                <img class="cisearch-result-cat-img" src="${cat.img}">
                <span>${cat.title}</span> 
                </a>
            </div>
            `;
            });
            result_box.append(
                `<span id="cisearch-result-h">دسته بندی ها</span>
            <div id="cisearch-result-cats">${cat_items}</div>`
            );
        }
        if ($("#items").length && cisearch_items.length > 0) {
            cisearch_items.forEach(item => {
                result_box.append(`
            <a class="cisearch-result-item" target="_blank" onclick="selectItem('${item.id}','${item.title}')">
                <img class="cisearch-result-item-img" src="${item.img}">
                <span class="cisearch-result-item-title">${item.title}</span> 
                <span class="cisearch-result-item-cat">${item.cat}</span>
            </a>
            `);
            });
        }
        if (cisearch_items.length == 0 && cisearch_categories.length == 0) {
            result_box.append(`<div id="cisearch-result-404">
             نتیجه ای پیدا نشد! </div>`);
        }
    }
}

function openCreateCat() {
    if ($("#create-cat-div").css("display") == "none") {
        $("#create-cat-div").show();
    } else {
        $("#create-cat-div").hide();
    }
}

let cis_categories = [];
let cis_items = [];
let cis_questions = [];

function selectCategory(category_id, category_title) {
    if (!cis_categories.includes(category_id)) {
        cis_categories.push(category_id);
        $("#categories").val(cis_categories.join(","));
        addSelectedCategory(category_id, category_title);
    }
}

function selectItem(item_id, item_title) {
    if (!cis_items.includes(item_id)) {
        cis_items.push(item_id);
        $("#items").val(cis_items.join(","));
        addSelectedItem(item_id, item_title);
    }
}

function selectQuestion(question_id, question_title) {
    if (!cis_questions.includes(question_id)) {
        cis_questions.push(question_id);
        $("#questions").val(cis_questions.join(","));
        addSelectedQuestion(question_id, question_title);
    }
}

function addSelectedCategory(category_id, category_title) {
    const categorySpan = $("<span>").text(category_title);
    categorySpan.on("click", () => {
        removeCategory(category_id, category_title);
    });
    $("#selected-categories").append(categorySpan);
}

function addSelectedItem(item_id, item_title) {
    const itemSpan = $("<span>").text(item_title);
    itemSpan.on("click", () => {
        removeItem(item_id, item_title);
    });
    $("#selected-items").append(itemSpan);
}

function addSelectedQuestion(question_id, question_title) {
    const questionSpan = $("<span>").text(question_title);
    questionSpan.on("click", () => {
        removeQuestion(question_id, question_title);
    });
    $("#selected-questions").append(questionSpan);
}

function removeCategory(category_id, category_title) {
    const index = cis_categories.indexOf(category_id);
    if (index !== -1) {
        cis_categories.splice(index, 1);
        $("#categories").val(cis_categories.join(","));
        $(`#selected-categories span`)
            .filter(function () {
                return $(this).text() === category_title;
            })
            .remove();
    }
}

function removeItem(item_id, item_title) {
    const index = cis_items.indexOf(item_id);
    if (index !== -1) {
        cis_items.splice(index, 1);
        $("#items").val(cis_items.join(","));
        $(`#selected-items span`)
            .filter(function () {
                return $(this).text() === item_title;
            })
            .remove();
    }
}

function removeQuestion(question_id, question_title) {
    const index = cis_questions.indexOf(question_id);
    if (index !== -1) {
        cis_questions.splice(index, 1);
        $("#questions").val(cis_questions.join(","));
        $(`#selected-questions span:contains("${question_title}")`).remove();
    }
}
