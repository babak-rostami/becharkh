$("#comImageModal").on("click", function (event) {
    if ($(event.target).attr("id") !== "com-img") {
        closeCommentModalImage();
    }
});

function clickCommentImg(imgId) {
    var modal = $("#comImageModal");
    var img = $(`#c-img-${imgId}`);
    var modalImg = $("#com-img");
    if (modal.css("display") == "none") {
        modal.css("display", "flex");
    }
    modalImg.attr("src", img.attr("src"));
    $("#close-com-img").click(function () {
        closeCommentModalImage();
    });
}

function closeCommentModalImage() {
    $("#comImageModal").hide();
}

function setPrevImageId(open_image_id) {
    if (open_image_id == 1) {
        prev_slide_image = parseInt(slide_count);
    } else {
        prev_slide_image = open_image_id - 1;
    }
}

function setNextImageId(open_image_id) {
    if (open_image_id == slide_count) {
        next_slide_image = 1;
    } else {
        next_slide_image = open_image_id + 1;
    }
}

if (cat_tips.length > 0) {
    const titleEl = document.getElementById("cat-abt-title");
    const descEl = document.getElementById("cat-abt-desc");

    let index = 0;

    async function typeDesc(text) {
        descEl.textContent = "";
        for (let char of text) {
            descEl.textContent += char;
            await new Promise(resolve => setTimeout(resolve, 80));
        }
    }

    async function loopContent() {
        while (true) {
            const item = cat_tips[index];
            titleEl.textContent = item.title;
            descEl.textContent = "";
            await new Promise(resolve => setTimeout(resolve, 400));
            await typeDesc(item.desc);
            await new Promise(resolve => setTimeout(resolve, 3000));
            index = (index + 1) % cat_tips.length;
        }
    }

    loopContent();
}

/* for bslider */
const catSlider = document.getElementById("cat-slider");
createSlider(catSlider, "cat-slider-item");
const ircatSlider = document.getElementById("ircat-slider");
createSlider(catSlider, "ircat-slider-item");
/* end for bslider */


// for comments tags
let can_select_tag = 1;
let tag_tick = $('#item-tag-tick-icon');
let loading_box = $('#com-tags-loading');
let loading_box_msg = $('#com-tags-loading-msg');
let tag2_container = $('#item-tags-2');

function selectItemTag(catId, itemId, tagId) {
    if (!can_select_tag) return;

    loadedCommentReplies = [];

    let currentSelected = $('.item-tag-selected').attr('id');
    if (currentSelected === 'item-tag-' + tagId) return;

    can_select_tag = 0;

    tag2_container.empty();
    tag2_container.hide();

    $('.item-tag-selected').removeClass('item-tag-selected').addClass('item-tag');

    tag_tick.detach();

    let selectedTag = $('#item-tag-' + tagId);
    selectedTag.removeClass('item-tag').addClass('item-tag-selected');
    selectedTag.append(tag_tick);

    loading_box.show();

    fetchComments(catId, itemId, tagId);
}

function selectItemTag2(catId, itemId, tagId) {
    if (!can_select_tag) return;

    let currentSelected = $('.item-tag-selected-2').attr('id');
    if (currentSelected === 'item-tag-' + tagId) return;

    can_select_tag = 0;

    $('.item-tag-selected-2').removeClass('item-tag-selected-2').addClass('item-tag-2');

    let selectedTag = $('#item-tag-' + tagId);
    selectedTag.removeClass('item-tag-2').addClass('item-tag-selected-2');

    loading_box.show();

    fetchComments(catId, itemId, tagId);
}

function fetchComments(catId, itemId, tagId) {
    let url = `/api/get-comments-page/${catId}/${itemId}/${tagId}/1/null/0`;
    let comment_box = $("#commentsBox");
    let showmorebtn = $("#load-more-com-btn");
    showmorebtn.hide();
    comment_box.empty();
    loading_box_msg.hide();

    $.ajax({
        url: url,
        type: "get",
        success: function (data) {
            nextPageUrl = data.nextPageUrl;

            if (Array.isArray(data.child_tags) && data.child_tags.length > 1) {
                showChildTags(data.category_id, data.item_id, data.child_tags);
            }

            if (data.no_cm != 1) {
                comment_box.append(data.html);
                scrollToId(data.scrollTo, 255);
            } else {
                loading_box_msg.text('نظری با این موضوع ثبت نشده است');
                loading_box_msg.show();
            }

            if (nextPageUrl) {
                showmorebtn.show();
            }

            loading_box.hide();
            can_select_tag = 1;
        },
        error: function (xhr, status, error) {
            if ($('#commentsBox').is(':empty')) {
                loading_box_msg.text('اتصال اینترنت خود را بررسی کنید');
                loading_box_msg.show();
                showmorebtn.hide();
                loading_box.hide();
            } else {
                showmorebtn.show();
            }
        }
    });
}

function showChildTags(category_id, item_id, tags) {
    tag2_container.empty();
    tag2_container.show();

    tags.forEach(function (tag) {
        let span = $('<span></span>')
            .addClass('item-tag-2')
            .attr('id', 'item-tag-' + tag.id)
            .text(tag.title)
            .attr('onclick', `selectItemTag2('${category_id}', '${item_id}', '${tag.id}')`);

        tag2_container.append(span);
    });
}

// end for comments tags