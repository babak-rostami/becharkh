let sasf_typingTimer;
let sasf_doneTypingInterval = 1000;

$("#sasf_cisearch_input").on("keyup", function () {
    clearTimeout(sasf_typingTimer);
    sasf_typingTimer = setTimeout(() => {
        sasf_cisearch_items = -1;
        sasf_cisearch_categories = -1;
        sasf_cisearch_questions = -1;
        sasf_cisearch_blogs = -1;
        sasfSearchForInput();
    }, sasf_doneTypingInterval);
});

// on keydown, clear the countdown
$("#sasf_cisearch_input").on("keydown", function (event) {
    if (event.key !== "Control" && !event.key.startsWith("Arrow")) {
        clearTimeout(sasf_typingTimer);
    }
});

let sasf_cisearch_for = 1;
let sasf_cisearch_input;
let sasf_cisearch_items = -1;
let sasf_cisearch_categories = -1;
let sasf_cisearch_questions = -1;

function sasfSearchForInput() {
    sasf_cisearch_input = $("#sasf_cisearch_input").val();

    if (sasf_cisearch_input?.length <= 2) {
        $("#sasf-show-cisearch-empty").show();
        $("#sasf-show-cisearch-result").hide();
        return;
    }
    $("#sasf-show-cisearch-result").hide();
    $("#sasf-show-cisearch-empty").hide();
    $("#sasf-show-cisearch-loading").show();

    $.ajax({
        method: "get",
        url: "/main-search/" + sasf_cisearch_for + "/" + sasf_cisearch_input,
        success: function (data) {
            $("#sasf-show-cisearch-loading").hide();
            $("#sasf-show-cisearch-result").show();
            if (sasf_cisearch_for == 1) {
                sasf_cisearch_items = data.items;
                sasf_cisearch_categories = data.categories;
                sasfShowMainSearchItemsResults();
            }
        }
    });
    if ($("#questions").length) {
        $.ajax({
            method: "get",
            url: "/main-search/2/" + sasf_cisearch_input,
            success: function (data) {
                if (sasf_cisearch_for == 1) {
                    sasf_cisearch_questions = data.questions;
                    sasfShowMainSearchForumResults();
                }
            }
        });
    }
    if ($("#videos").length) {
        $.ajax({
            method: "get",
            url: "/main-search/4/" + sasf_cisearch_input,
            success: function (data) {
                if (sasf_cisearch_for == 1) {
                    sasf_cisearch_videos = data.videos;
                    sasfShowMainSearchVideoResults();
                }
            }
        });
    }
}

function sasfShowMainSearchVideoResults() {
    result_box = $("#sasf-show-cisearch-result");
    if (sasf_cisearch_videos.length > 0) {
        sasf_cisearch_videos.forEach(video => {
            result_box.append(
                `<a class="sasf-cisearch-video" onclick="sasfSelectVideo('${video.id}', '${video.title}')">
                ${video.title}</a>`
            );
        });
    } else {
        result_box.append(`<div id="sasf-cisearch-404">
             نتیجه ای پیدا نشد! </div>`);
    }
}
function sasfShowMainSearchForumResults() {
    result_box = $("#sasf-show-cisearch-result");
    if (sasf_cisearch_questions.length > 0) {
        sasf_cisearch_questions.forEach(question => {
            result_box.append(
                `<a class="sasf-cisearch-question" onclick="sasfSelectQuestion('${question.id}', '${question.title}')">
                ${question.title}</a>`
            );
        });
    } else {
        result_box.append(`<div id="sasf-cisearch-404">
             نتیجه ای پیدا نشد! </div>`);
    }
}
function sasfShowMainSearchItemsResults() {
    sasf_cisearch_for = 1;
    let result_box;
    result_box = $("#sasf-show-cisearch-result");
    result_box.empty();
    if (sasf_cisearch_categories == -1 || sasf_cisearch_items == -1) {
        sasfSearchForInput();
    } else {
        if (sasf_cisearch_categories.length > 0) {
            let cat_items = "";
            sasf_cisearch_categories.forEach(cat => {
                cat_items += `<div class="sasf-cisearch-result-cat">
                <a class="sasf-cisearch-result-cat-a" target="_blank"  onclick="sasfSelectCategory('${cat.id}','${cat.title}')">
                <img class="sasf-cisearch-result-cat-img" src="${cat.img}">
                <span>${cat.title}</span> 
                </a>
            </div>
            `;
            });
            result_box.append(
                `<span id="sasf-cisearch-result-h">دسته بندی ها</span>
            <div id="sasf-cisearch-result-cats">${cat_items}</div>`
            );
        }
        if (sasf_cisearch_items.length > 0) {
            sasf_cisearch_items.forEach(item => {
                result_box.append(`
            <a class="sasf-cisearch-result-item" target="_blank" onclick="sasfSelectItem('${item.id}','${item.title}')">
                <img class="sasf-cisearch-result-item-img" src="${item.img}">
                <span class="sasf-cisearch-result-item-title">${item.title}</span> 
                <span class="sasf-cisearch-result-item-cat">${item.cat}</span>
            </a>
            `);
            });
        }
        if (sasf_cisearch_items.length == 0 && sasf_cisearch_categories.length == 0) {
            result_box.append(`<div id="sasf-cisearch-result-404">
             نتیجه ای پیدا نشد! </div>`);
        }
    }
}

function sasfOpenCreateCat() {
    if ($("#sasf-create-cat-div").css("display") == "none") {
        $("#sasf-create-cat-div").show();
    } else {
        $("#sasf-create-cat-div").hide();
    }
}

let sasf_categories = [];
let sasf_items = [];
let sasf_questions = [];
let sasf_video = null;

function sasfSelectCategory(category_id, category_title) {
    if (!sasf_categories.includes(category_id)) {
        sasf_categories.push(category_id);
        $("#sasf-categories").val(sasf_categories.join(","));
        sasfAddSelectedCategory(category_id, category_title);
    }
}

function sasfSelectItem(item_id, item_title) {
    if (!sasf_items.includes(item_id)) {
        sasf_items.push(item_id);
        $("#sasf-items").val(sasf_items.join(","));
        sasfAddSelectedItem(item_id, item_title);
    }
}

function sasfSelectQuestion(question_id, question_title) {
    if (!sasf_questions.includes(question_id)) {
        sasf_questions.push(question_id);
        $("#sasf-questions").val(sasf_questions.join(","));
        sasfAddSelectedQuestion(question_id, question_title);
    }
}

function sasfSelectVideo(video_id, video_title) {
    if (sasf_video != video_id) {
        sasf_video = video_id;
        $("#sasf-video").val(sasf_video);
        sasfAddSelectedVideo(video_id, video_title);
    }
}

function sasfAddSelectedCategory(category_id, category_title) {
    const categorySpan = $("<span>").text(category_title);
    categorySpan.on("click", () => {
        sasfRemoveCategory(category_id, category_title);
    });
    $("#sasf-selected-categories").append(categorySpan);
}

function sasfAddSelectedItem(item_id, item_title) {
    const itemSpan = $("<span>").text(item_title);
    itemSpan.on("click", () => {
        sasfRemoveItem(item_id, item_title);
    });
    $("#sasf-selected-items").append(itemSpan);
}

function sasfAddSelectedQuestion(question_id, question_title) {
    const questionSpan = $("<span>").text(question_title);
    questionSpan.on("click", () => {
        sasfRemoveQuestion(question_id, question_title);
    });
    $("#sasf-selected-questions").append(questionSpan);
}

function sasfAddSelectedVideo(video_id, video_title) {
    const videoSpan = $("<span>").text(video_title);
    videoSpan.on("click", () => {
        sasfRemoveVideo(video_id, video_title);
    });
    $("#sasf-selected-video").empty();
    $("#sasf-selected-video").append(videoSpan);
}

function sasfRemoveCategory(category_id, category_title) {
    let index = sasf_categories.indexOf(category_id);
    if (index !== -1) {
        sasf_categories.splice(index, 1);
        $("#sasf-categories").val(sasf_categories.join(","));
        $(`#sasf-selected-categories span`)
            .filter(function () {
                return $(this).text() === category_title;
            })
            .remove();
    }
}

function sasfRemoveItem(item_id, item_title) {
    let index = sasf_items.indexOf(item_id);
    if (index !== -1) {
        sasf_items.splice(index, 1);
        $("#sasf-items").val(sasf_items.join(","));
        $(`#sasf-selected-items span`)
            .filter(function () {
                return $(this).text() === item_title;
            })
            .remove();
    }
}

function sasfRemoveQuestion(question_id, question_title) {
    let index = sasf_questions.indexOf(question_id);
    if (index !== -1) {
        sasf_questions.splice(index, 1);
        $("#sasf-questions").val(sasf_questions.join(","));
        $(`#sasf-selected-questions span:contains("${question_title}")`).remove();
    }
}

function sasfRemoveVideo(video_id, video_title) {
    if (sasf_video == video_id) {
        sasf_video = null;
        $("#sasf-video").val("");
        $(`#sasf-selected-video span:contains("${video_title}")`).remove();
    }
}
