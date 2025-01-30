var typingTimer; // timer identifier
var doneTypingInterval = 1000; // time in ms, 1 second for example

// on keyup, start the countdown
$("#cisearch_input").on("keyup", function() {
    clearTimeout(typingTimer);

    typingTimer = setTimeout(() => {
        cisearch_items = -1;
        cisearch_categories = -1;
        cisearch_questions = -1;
        cisearch_blogs = -1;
        searchForInput();
    }, doneTypingInterval);
});

// on keydown, clear the countdown
$("#cisearch_input").on("keydown", function(event) {
    if (event.key !== "Control" && !event.key.startsWith("Arrow")) {
        clearTimeout(typingTimer);
    }
});

let cisearch_for = 1;
let cisearch_input;
let cisearch_items = -1;
let cisearch_categories = -1;
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
        success: function(data) {
            $("#show-cisearch-loading").hide();
            $("#show-cisearch-result").show();
            if (cisearch_for == 1) {
                cisearch_items = data.items;
                cisearch_categories = data.categories;
                showMainSearchItemsResults();
            }
        }
    });
}

function showMainSearchItemsResults() {
    cisearch_for = 1;
    let result_box;
    result_box = $("#show-cisearch-result");
    result_box.empty();
    if (cisearch_categories == -1 || cisearch_items == -1) {
        searchForInput();
    } else {
        if (cisearch_categories.length > 0) {
            let cat_items = "";
            cisearch_categories.forEach(cat => {
                cat_items += `<div class="cisearch-result-cat">
                <a class="cisearch-result-cat-a" target="_blank" href="category/${cat.id}">
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
        if (cisearch_items.length > 0) {
            cisearch_items.forEach(item => {
                result_box.append(`
            <a class="cisearch-result-item" target="_blank" href="item-edit/${item.id}">
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

const editors = document.querySelectorAll(".ckeditor");
editors.forEach(editor => {
    ClassicEditor.create(editor, {
        language: "fa"
    });
});
