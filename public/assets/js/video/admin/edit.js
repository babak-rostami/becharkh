function updateVideo() {
    // Prevent the default action (submitting the form or refreshing the page)
    $(".input-char-min").hide();
    $(".input-char-max").hide();
    var tmp = 0;
    var count = 0;

    //validate title
    if ($("#title").val().length < 15 || $("#title").val().length > 60) {
        $("#title-error").show();
        $("#title-error").text(
            "عنوان ویدیو خوب باید بین 15 تا 60 کاراکتر باشد"
        );
        tmp = 1;
    } else {
        count++;
    }

    //validate description
    if ($("#description").val().length < 15) {
        $("#description-error").show();
        $("#description-error").text(
            "توضیحات ویدیو خوب باید حداقل 15 کاراکتر باشد"
        );
        tmp = 1;
    } else {
        count++;
    }

    if (tmp == 1) {
        $(".error").show();
        $(".error").css("display", "block");
        $(".error").text(
            "برای بهتر دیده شدن ویدیو خطاهای گفته شده زیر هر بخش را برطرف کنید"
        );
    } else {
        if (uploading == 1) {
            $(".error").show();
            $(".error").css("display", "block");
            $(".error").text("در حال آپلود ویدیو... منتظر بمانید");
        }
    }

    updateBtn = $("#updateVideoBtn");

    if (count == 2 && uploading == 0) {
        updateBtn
            .html(loadingGif)
            .addClass("btn btn-light mb-2 w-100")
            .prop("disabled", true);
        $(`#${submit_form_id}`).submit();
    }
}

function readURL(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            id = "#blah";
            $(id).attr("src", e.target.result);
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function countCharacters(input, min = null, max = null) {
    var currentLength = $(input).val().length;
    var maxLength = max;
    var checkMax = 0;
    var minLength = min;
    var charMinSpanId = "#charCountMin-" + input.id;
    var CharMinSpan = $(charMinSpanId);
    var charMaxSpanId = "#charCountMax-" + input.id;
    var CharMaxSpan = $(charMaxSpanId);

    $(".error").hide();
    if (input.id == "title") {
        $("#title-error").hide();
    }
    if (input.id == "description") {
        $("#description-error").hide();
    }

    if (minLength != null) {
        var remainingCharMin = minLength - currentLength;
        if (remainingCharMin < 0) {
            remainingCharMin = 0;
        }
        if (currentLength >= minLength) {
            CharMinSpan.hide();
            checkMax = 1;
        } else {
            checkMax = 0;
            CharMinSpan.show();
            CharMaxSpan.hide();
            CharMinSpan.text(
                "حداقل " + remainingCharMin + " کاراکتر دیگر وارد کنید"
            );
        }
    }
    if (maxLength != null && checkMax == 1) {
        CharMaxSpan.show();
        var remainingCharMax = maxLength - currentLength;
        if (remainingCharMax < 0) {
            remainingCharMax = 0;
        }
        if (currentLength > maxLength) {
            $(input).val(
                $(input)
                    .val()
                    .substring(0, maxLength)
            ); // Truncate the input value to the maximum limit
        }
        CharMaxSpan.text("تعداد کاراکتر باقی مانده : " + remainingCharMax);
    }
}

function hideEditorError() {
    $("#editor-error").hide();
    $(".error").hide();
}

function changeChildrens(item, fea_id) {
    var item_id = item.value;
    axios.get("/feature-children-item/" + fea_id + "/" + item_id).then(
        response => (
            $.each(response.data.all_child, function (i, value) {
                var id = "#" + value.slug;
                $(id).empty();
                $(id).append(
                    "<option value=" + null + ">انتخاب نشده است</option>"
                );
            }),
            $.each(response.data.child_items, function (i, value) {
                $(response.data.child_slug).append(
                    "<option value=" +
                    value.id +
                    ">" +
                    value.title +
                    "</option>"
                );
            })
        )
    );
}

function howManyDiamondNeedForSize(size) {
    if (size < 11) {
        return 0;
    } else if (size >= 11 && size < 26) {
        return 8000;
    } else if (size >= 26 && size < 51) {
        return 12000;
    } else if (size >= 51 && size < 101) {
        return 18000;
    } else if (size >= 101 && size < 176) {
        return 26000;
    } else if (size >= 176 && size < 251) {
        return 38000;
    } else if (size >= 251 && size < 351) {
        return 48000;
    } else if (size >= 351 && size < 451) {
        return 56000;
    } else if (size >= 451 && size < 501) {
        return 64000;
    }
}

function videoAdded() {
    $(".error").hide();
    $("#video-error").hide();
    videoInput = $("#video-file-input");
    var file = videoInput[0].files[0];
    var fileSize = file.size;

    // convert bytes to megabytes
    var videoSize = fileSize / 1048576;
    var videoSizeM = videoSize.toFixed(2);

    if (videoSizeM > 500) {
        sizeText = "حداکثر حجم مجاز ویدیو 500 مگابایت می باشد";
        $("#browseVideoFile").text("تغییر ویدیو");
        $("#video-size-info").text(sizeText);
        $("#video-require-info").show();
        $("#need-diamond-info").hide();
        $("#user-diamond-info").hide();
        $("#go_to_d_plan").hide();
        return;
    }

    sizeText = `حجم ویدیو شما ${videoSizeM} مگابایت می باشد`;

    if (video_max_size >= Math.floor(videoSizeM)) {
        needNumber = 0;
    } else {
        needNumber =
            howManyDiamondNeedForSize(videoSizeM) -
            howManyDiamondNeedForSize(video_max_size - 5);
    }
    if (needNumber == 0) {
        needNumber = "رایگان";
    } else {
        needNumber = `${needNumber} هزار تومان`;
    }
    needText = `موجودی مورد نیاز : ${needNumber}`;
    userDiamondCountText = `موجودی شما : ${user_diamond_count} هزار تومان`;
    if (parseInt(needNumber) > parseInt(user_diamond_count)) {
        $("#go_to_d_plan").show();
    } else {
        $("#go_to_d_plan").hide();
        $("#start-upload-video-btn").show();
    }

    $("#browseVideoFile").text("تغییر ویدیو");
    $("#video-size-info").text(sizeText);
    $("#need-diamond-info").text(needText);
    $("#user-diamond-info").text(userDiamondCountText);
    $("#video-require-info").show();
    $("#need-diamond-info").show();
    $("#user-diamond-info").show();

    $("#video-upload-suc").hide();
    $("#video-upload-er").hide();
    $("#video-uploading").hide();
}

function startUploadVideo() {
    uploading = 1;
    $("#video-error").hide();
    $(".error").hide();
    $("#video-upload-info").show();
    $("#video-uploading").show();
    $("#proccess-video").show();
    $("#hide-upload-btn").hide();
    $("#video-require-info").hide();
    $("#video-upload-er").hide();
    const videoFile = $("#video-file-input")[0].files[0];
    const formData = new FormData();
    formData.append("file", videoFile);
    formData.append("category_id", category_id);
    formData.append("video_id", video_id);

    var xhr = new XMLHttpRequest();
    xhr.open("POST", video_file_store_route);
    xhr.setRequestHeader("X-CSRF-TOKEN", video_edit_csrf);
    xhr.upload.onprogress = event => {
        if (event.lengthComputable) {
            const progress = Math.round((event.loaded / event.total) * 100);
            document.getElementById("proccess-video").value = progress;
        }
    };
    xhr.onload = () => {
        if (xhr.status === 200) {
            uploading = 0;
            const response = JSON.parse(xhr.responseText);
            $("#video-uploading").hide();
            $("#proccess-video").hide();
            $("#video-upload-suc").show();
            $(".error").hide();
            $("#video-error").hide();
            return;
        } else {
            uploading = 0;
            const response = JSON.parse(xhr.responseText);
            if (response.error) {
                $("#video-upload-er").text(response.error);
            }
            $("#video-uploading").hide();
            $("#proccess-video").hide();
            $("#video-upload-er").show();
            $("#hide-upload-btn").show();
            $("#video-require-info").show();
            return 0;
        }
    };
    xhr.send(formData);
}

const titleInput = document.getElementById("title");

titleInput.addEventListener("keypress", function (event) {
    if (event.keyCode === 13) {
        event.preventDefault();
        updateVideo();
    }
});

// for select category for cu modal
function findCategoryForSCFCE(cat_id) {
    for (let i = 0; i < categories.length; i++) {
        if (categories[i].id == cat_id) {
            return categories[i];
        }
    }
    return categories[i];
}
function changeCategoryChildrenForSCFCE(cat_id) {
    cat_children = [];
    categories.forEach(category => {
        if (category.p_id == cat_id) {
            cat_children.push(category);
        }
    });
}
function hasChildrenForSCFCE(cat_id) {
    for (let i = 0; i < categories.length; i++) {
        if (categories[i].p_id == cat_id) {
            return 1;
        }
    }
    return 0;
}

function showCatChildrenModalForSCFCE(cat_id) {
    new_list = "";
    show_div = $("#show_categories_list_ch_modal");
    let modal_ce_cat_selected = null;
    if (cat_id == 0) {
        cat_children = [];
        categories.forEach(cat => {
            if (cat.p_id == null) {
                cat_children.push(cat);
            }
        });
    } else {
        modal_ce_cat_selected = findCategoryForSCFCE(cat_id);
        changeCategoryChildrenForSCFCE(cat_id);
        if (modal_ce_cat_selected.p_id == null) {
            new_list =
                `<div class="col-12 filter-cat-return-box-desk cur-p text-right"
        onclick="showCatChildrenModalForSCFCE(0)">
        <img src="` +
                back_cat_img +
                `">
        <span>بازگشت</span>
        </div>`;
        } else {
            new_list =
                `<div class="col-12 filter-cat-return-box-desk cur-p text-right"
        onclick="showCatChildrenModalForSCFCE('` +
                modal_ce_cat_selected.p_id +
                `')">
        <img src="` +
                back_cat_img +
                `">
        <span>بازگشت</span>
        </div>`;
        }
    }
    cat_children.forEach(child => {
        if (hasChildrenForSCFCE(child.id)) {
            new_list +=
                `<div class="col-12 filter-cat-box-desk cur-p text-right"
            onclick="showCatChildrenModalForSCFCE('` +
                child.id +
                `')">
            <span>` +
                child.title +
                `</span>
            <img class="float-left" src="` +
                next_cat_img +
                `">
            </div>`;
        } else {
            new_list +=
                `<div class="col-12 filter-cat-box-desk text-right p-0" data-dismiss="modal" onclick="selectCatItemForSCFCE('${child.id}')">
                            <div class="cat-item-ch-cat-modal cur-p">` +
                child.title +
                `</div>
                        </div>`;
        }
    });
    if (cat_id != 0) {
        t = "مطالب مرتبط با ";
        new_list +=
            `<div class="col-12 filter-cat-all-box-desk text-right p-0" data-dismiss="modal" onclick="selectCatItemForSCFCE('${modal_ce_cat_selected.id}')">
                    <div class="cat-item-ch-cat-modal cur-p">` +
            `<img class="ml-2" src="` +
            all_cat_img +
            `"></img>` +
            t +
            modal_ce_cat_selected.title +
            `</div>
                </div>`;
    }
    show_div.html(new_list);
}

function showCategoryInput() {
    let c = findCategoryForSCFCE(category_id);
    $("#category_id").val(c.id);
    $("#modcat-select-input").text(c.title);
}

function selectCatItemForSCFCE(category_id) {
    if (category_id != cat_selected) {
        $("#upload-video").show();
        $("#features-box").empty();
        $("#cf-inputs").empty();
        let c = findCategoryForSCFCE(category_id);
        $("#category_id").val(c.id);
        $("#modcat-select-input").text(c.title);
        cat_selected = category_id;
        // $.ajax({
        //     url: get_cat_fis_route,
        //     method: "GET",
        //     data: {
        //         category_id: category_id
        //     },
        //     success: function (data) {
        //         cfeatures = data.cfeatures;
        //         citems = data.citems;
        //         feature_items = null;
        //         showChildrenFeaturesBoxs();
        //     }
        // });
    }
}

function findCategoryWithTitleForSCFCE(cat_title) {
    for (let i = 0; i < categories.length; i++) {
        if (categories[i].title == cat_title) {
            return categories[i];
        }
    }
    return null;
}

function getAllChildrenCategories(categoryId) {
    let childrenCategories = [];
    let currentCategory = categories.find(
        category => category.id === categoryId
    );
    if (currentCategory) {
        childrenCategories.push(currentCategory);
        let subCategories = categories.filter(
            category => category.p_id === currentCategory.id
        );
        subCategories.forEach(subCategory => {
            childrenCategories = childrenCategories.concat(
                getAllChildrenCategories(subCategory.id)
            );
        });
    }
    return childrenCategories;
}

function modcatSelectSearchCategories() {
    let search = $("#search-categories-ch-modal-input")
        .val()
        .toLowerCase();
    // Get the categories
    let categories_div = $(`.filter-cat-box-desk`);
    // Hide all categories
    categories_div.hide();

    categories_div.filter((_, category_div) => {
        let category_title = $(category_div)
            .find("span")
            .text()
            .toLowerCase();
        if (category_title == "") {
            category_title = $(category_div)
                .find("div")
                .text()
                .toLowerCase();
        }
        let category = findCategoryWithTitleForSCFCE(category_title);
        if (category) {
            let childCategories = getAllChildrenCategories(category.id);
            let showCategory = false;
            childCategories.forEach(chCat => {
                if (
                    chCat.title.includes(search.toLowerCase()) ||
                    chCat.slug.includes(search.toLowerCase())
                ) {
                    showCategory = true;
                }
            });
            if (showCategory) {
                $(category_div).show(); // Wrap category_div with jQuery
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    });
}

let timeoutSugCatId = null;
function suggestCategories(input) {
    if (timeoutSugCatId) {
        clearTimeout(timeoutSugCatId);
    }
    timeoutSugCatId = setTimeout(() => {
        let title = input.value.toLowerCase();
        let sug_box = $("#sug-cat-box");
        let show_box = 0;
        $(".sug-cat").remove();
        categories.forEach(category => {
            clearStepWords(title).forEach(word => {
                if (
                    category.title.includes(word) ||
                    category.slug.includes(word) ||
                    category.ss?.includes(word)
                ) {
                    let sug_cat = `<span class="sug-cat" onclick="selectCatItemForSCFCE('${category.id}')">${category.title}</span>`;
                    sug_box.append(sug_cat);
                    show_box = 1;
                }
            });
        });
        if (show_box == 1) {
            sug_box.show();
        } else {
            sug_box.hide();
        }
    }, 5000);
}

function clearStepWords(text) {
    const words = text.split(" ");
    const stepWords = [
        "را",
        "ده",
        "های",
        "ان",
        "خرید",
        "فروش",
        "او",
        "کی",
        "سر",
        "باز",
        "پا",
        "دست",
        "روز",
        "این",
        "شد",
        "گر",
        "بد",
        "جا",
        "برای",
        "که",
        "کم",
        "یک",
        "دو",
        "سه",
        "کرد",
        "چرا",
        "تا",
        "می",
        "پر",
        "بی",
        "ترین",
        "ای",
        "با",
        "چه",
        "یک",
        "نه",
        "یا",
        "رو",
        "در",
        "به",
        "از",
        "سلام"
    ];
    const filteredWords = words.filter(
        word => word.length > 2 && !stepWords.includes(word.toLowerCase())
    );
    return filteredWords;
}